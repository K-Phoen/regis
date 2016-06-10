<?php

declare(strict_types=1);

namespace Regis\Github;

use Symfony\Component\HttpFoundation\Request;

class PayloadValidator
{
    private $repositories;

    public function __construct(array $repositories)
    {
        $this->repositories = $repositories;
    }

    public function validate(Request $request)
    {
        $signature = $request->headers->get('X-Hub-Signature');

        if (empty($signature)) {
            throw Exception\PayloadSignature::missing();
        }

        $rawPayload = $request->getContent();
        $payload = json_decode($rawPayload, true);
        $repository = $this->extractRepository($payload);

        if (!array_key_exists($repository, $this->repositories)) {
            throw Exception\PayloadSignature::unknownRepository($repository);
        }

        $signatureParts = explode('=', $signature, 2);

        if (count($signatureParts) !== 2) {
            throw Exception\PayloadSignature::invalid();
        }

        list($algorithm, $hash) = $signatureParts;
        if (!in_array($algorithm, hash_algos(), true)) {
            throw Exception\PayloadSignature::unknownAlgorithm($algorithm);
        }

        $secret = $this->repositories[$repository]['secret'];
        $expectedSignature = hash_hmac('sha1', $rawPayload, $secret);

        if (!hash_equals($expectedSignature, $hash)) {
            throw Exception\PayloadSignature::invalid();
        }
    }

    private function extractRepository(array $payload): string
    {
        if (empty($payload['repository']) || empty($payload['repository']['full_name'])) {
            throw Exception\PayloadSignature::couldNotDetermineRepository();
        }

        return $payload['repository']['full_name'];
    }
}