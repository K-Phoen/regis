<?php

declare(strict_types=1);

namespace Regis\Github;

use Github\HttpClient\HttpClientInterface;
use Regis\Domain\Model\Github as Model;

class Client
{
    private $client;
    private $apiToken;
    private $authenticated = false;

    public function __construct(HttpClientInterface $httpClient, string $apiToken)
    {
        // TODO logs
        $this->client = new \Github\Client($httpClient);
        $this->apiToken = $apiToken;
    }

    public function sendComment(Model\PullRequest $pullRequest, Model\ReviewComment $comment)
    {
        $this->assertAuthenticated();

        $repository = $pullRequest->getRepository();

        $this->client->api('pull_request')->comments()->create($repository->getOwner(), $repository->getName(), $pullRequest->getNumber(), [
            'commit_id' => $pullRequest->getHead(),
            'path' => $comment->getFile(),
            'position' => $comment->getPosition(),
            'body' => $comment->getContent(),
        ]);
    }

    private function assertAuthenticated()
    {
        if (!$this->authenticated) {
            $this->client->authenticate($this->apiToken, '', \Github\Client::AUTH_URL_TOKEN);
            $this->authenticated = true;
        }
    }
}