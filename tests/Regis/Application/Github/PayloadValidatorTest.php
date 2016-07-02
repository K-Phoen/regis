<?php

namespace Tests\Regis\Application\Github;

use Symfony\Component\HttpFoundation\Request;

use Regis\Infrastructure\Repository;
use Regis\Application\Github\Exception\PayloadSignature;
use Regis\Application\Github\PayloadValidator;

class PayloadValidatorTest extends \PHPUnit_Framework_TestCase
{
    const REPOSITORIES = [
        'k-phoen/test' => [
            'secret' => 'some_awesome_secret',
        ]
    ];

    /** @var PayloadValidator */
    private $payloadValidator;

    public function setUp()
    {
        $repositoriesRepo = new Repository\InMemoryRepositories(self::REPOSITORIES);
        $this->payloadValidator = new PayloadValidator($repositoriesRepo);
    }

    public function testValidRequestsAreAccepted()
    {
        $this->payloadValidator->validate($this->validRequest());
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
            [ $this->unsignedRequest(), 'Payload signature is missing.' ],
            [ $this->noRepositoryRequest(), 'Could not determine the repository associated to the payload.' ],
            [ $this->unknownRepositoryRequest(), 'Repository "k-phoen/unknown" is not known.' ],
            [ $this->noSignatureAlgorithmRequest(), 'Payload signature is invalid.' ],
            [ $this->unknownSignatureAlgorithmRequest(), 'Algorithm "lala" is not known.' ],
            [ $this->invalidSignatureRequest(), 'Payload signature is invalid.' ],
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
        $signature = hash_hmac('sha1', $payload, self::REPOSITORIES['k-phoen/test']['secret']);

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
        return <<<PAYLOAD
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
        return <<<PAYLOAD
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
        return <<<PAYLOAD
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
