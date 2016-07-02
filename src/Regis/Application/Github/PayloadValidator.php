<?php

declare(strict_types=1);

namespace Regis\Application\Github;

use Symfony\Component\HttpFoundation\Request;

use Regis\Domain\Repository;

class PayloadValidator
{
    private $repositoriesRepo;

    public function __construct(Repository\Repositories $repositoriesRepo)
    {
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function validate(Request $request)
    {
        $signature = $request->headers->get('X-Hub-Signature');

        if (empty($signature)) {
            throw Exception\PayloadSignature::missing();
        }

        $rawPayload = $request->getContent();
        $payload = json_decode($rawPayload, true);
        $repositoryIdentifier = $this->extractRepositoryIdentifier($payload);

        try {
            $repository = $this->repositoriesRepo->find($repositoryIdentifier);
        } catch (Repository\Exception\NotFound $e) {
            throw Exception\PayloadSignature::unknownRepository($repositoryIdentifier, $e);
        }

        $signatureParts = explode('=', $signature, 2);

        if (count($signatureParts) !== 2) {
            throw Exception\PayloadSignature::invalid();
        }

        list($algorithm, $hash) = $signatureParts;
        if (!in_array($algorithm, hash_algos(), true)) {
            throw Exception\PayloadSignature::unknownAlgorithm($algorithm);
        }

        $expectedSignature = hash_hmac('sha1', $rawPayload, $repository->getSharedSecret());

        if (!hash_equals($expectedSignature, $hash)) {
            throw Exception\PayloadSignature::invalid();
        }
    }

    private function extractRepositoryIdentifier(array $payload): string
    {
        if (empty($payload['repository']) || empty($payload['repository']['full_name'])) {
            throw Exception\PayloadSignature::couldNotDetermineRepository();
        }

        return $payload['repository']['full_name'];
    }
}
