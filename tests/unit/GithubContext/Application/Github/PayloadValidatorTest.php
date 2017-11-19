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

namespace Tests\Regis\GithubContext\Application\Github;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Regis\GithubContext\Application\Github\Exception\PayloadSignature;
use Regis\GithubContext\Application\Github\PayloadValidator;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Infrastructure\Repository;
use Tests\Regis\Helper\ObjectManipulationHelper;

class PayloadValidatorTest extends TestCase
{
    use ObjectManipulationHelper;

    /** @var PayloadValidator */
    private $payloadValidator;

    public function setUp()
    {
        $admin = $this->createMock(Entity\UserAccount::class);

        $repository = new Entity\Repository();
        $this->setPrivateValue($repository, 'owner', $admin);
        $this->setPrivateValue($repository, 'identifier', 'k-phoen/test');
        $this->setPrivateValue($repository, 'sharedSecret', 'some_awesome_secret');

        $repositoriesRepo = new Repository\InMemoryRepositories([
            $repository,
        ]);

        $this->payloadValidator = new PayloadValidator($repositoriesRepo);
    }

    public function testValidRequestsAreAccepted()
    {
        $this->payloadValidator->validate($this->validRequest());
        $this->assertTrue(true, 'No exception is raised');
    }

    /**
     * @dataProvider invalidRequests
     */
    public function testInvalidRequestsAreRejected(Request $request, string $expectedError)
    {
        $this->expectException(PayloadSignature::class);
        $this->expectExceptionMessage($expectedError);

        $this->payloadValidator->validate($request);
    }

    public function invalidRequests()
    {
        return [
            [$this->unsignedRequest(), 'Payload signature is missing.'],
            [$this->noRepositoryRequest(), 'Could not determine the repository associated to the payload.'],
            [$this->unknownRepositoryRequest(), 'Repository "k-phoen/unknown" is not known.'],
            [$this->noSignatureAlgorithmRequest(), 'Payload signature is invalid.'],
            [$this->unknownSignatureAlgorithmRequest(), 'Algorithm "lala" is not known.'],
            [$this->invalidSignatureRequest(), 'Payload signature is invalid.'],
        ];
    }

    private function unsignedRequest(): Request
    {
        return new Request();
    }

    private function unknownRepositoryRequest(): Request
    {
        return $this->requestWithContent($this->unknownRepositoryPayload(), 'dummy signature');
    }

    private function noRepositoryRequest(): Request
    {
        return $this->requestWithContent($this->noRepositoryPayload(), 'dummy signature');
    }

    private function noSignatureAlgorithmRequest(): Request
    {
        return $this->requestWithContent($this->knownRepositoryPayload(), 'dummy signature');
    }

    private function unknownSignatureAlgorithmRequest(): Request
    {
        return $this->requestWithContent($this->knownRepositoryPayload(), 'lala=dummy_signature');
    }

    private function invalidSignatureRequest(): Request
    {
        return $this->requestWithContent($this->knownRepositoryPayload(), 'sha1=dummy_signature');
    }

    private function validRequest(): Request
    {
        $payload = $this->knownRepositoryPayload();
        $signature = hash_hmac('sha1', $payload, 'some_awesome_secret');

        return $this->requestWithContent($payload, 'sha1='.$signature);
    }

    private function requestWithContent(string $content, string $signature): Request
    {
        return Request::create(
            '/github/webhook', 'GET',
            $parameters = [], $cookies = [], $files = [],
            $server = ['HTTP_X-Hub-Signature' => $signature],
            $content
        );
    }

    private function unknownRepositoryPayload(): string
    {
        return <<<'PAYLOAD'
{
  "action": "opened",
  "number": 2,
  "pull_request": {
      ".": "."
  },
  "repository": {
    "full_name": "k-phoen/unknown",
     ".": "."
  }
}
PAYLOAD;
    }

    private function noRepositoryPayload(): string
    {
        return <<<'PAYLOAD'
{
  "action": "opened",
  "number": 2,
  "pull_request": {
      ".": "."
  }
}
PAYLOAD;
    }

    private function knownRepositoryPayload(): string
    {
        return <<<'PAYLOAD'
{
  "action": "opened",
  "number": 2,
  "pull_request": {
      ".": "."
  },
  "repository": {
    "full_name": "k-phoen/test",
     ".": "."
  }
}
PAYLOAD;
    }
}
