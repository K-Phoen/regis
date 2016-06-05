<?php

declare(strict_types=1);

namespace Regis\Github;

use Github\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Regis\Domain\Model\Github as Model;

class Client
{
    private $client;
    private $apiToken;
    private $logger;
    private $authenticated = false;

    public function __construct(\Github\Client $client, string $apiToken, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->apiToken = $apiToken;
        $this->logger = $logger;
    }

    public function createWebhook(string $owner, string $repository, string $url, $secret = null)
    {
        $this->assertAuthenticated();

        $this->logger->info('Creating webhook for {repository} to call {url}', [
            'repository' => sprintf('%s/%s', $owner, $repository),
            'url' => $url,
        ]);

        //var_dump($this->client->api('repo')->hooks()->all($owner, $repository));

        $this->client->api('repo')->hooks()->create($owner, $repository, [
            'name' => 'web',
            'config' => [
                'url' => $url,
                'content_type' => 'json',
                'secret' => $secret,
            ],
            'events' => ['*'],
            'active' => true,
        ]);
    }

    public function sendComment(Model\PullRequest $pullRequest, Model\ReviewComment $comment)
    {
        $this->assertAuthenticated();

        $repository = $pullRequest->getRepository();

        $this->logger->info('Sending review comment for PR {pull_request} -- {commit_id}@{path}:{position} -- {comment}', [
            'pull_request' => sprintf('%s#%d', $repository, $pullRequest->getNumber()),
            'commit_id' => $pullRequest->getHead(),
            'path' => $comment->getFile(),
            'position' => $comment->getPosition(),
            'comment' => $comment->getContent(),
        ]);

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