<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Regis\GithubContext\Application\Github;

use Symfony\Component\HttpFoundation\Request;
use Regis\GithubContext\Domain\Repository;

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
