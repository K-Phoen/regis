<?php

declare(strict_types=1);

namespace Regis\GithubContext\Infrastructure\Github;

use Psr\Log\LoggerInterface as Logger;
use Regis\GithubContext\Application\Github\IntegrationStatus;

use Regis\GithubContext\Application\Github\Client as GithubClient;
use Regis\GithubContext\Domain\Entity\User;
use Regis\GithubContext\Domain\Model;

class Client implements GithubClient
{
    private $client;
    private $user;
    private $logger;
    private $authenticated = false;

    public function __construct(\Github\Client $client, User $user, Logger $logger)
    {
        $this->client = $client;
        $this->user = $user;
        $this->logger = $logger;
    }

    public function listRepositories(): \Traversable
    {
        $this->assertAuthenticated();

        $this->logger->info('Fetching repositories list for user {username}', [
            'username' => $this->user->getUsername(),
        ]);
        $api = $this->client->currentUser();
        $paginator = new \Github\ResultPager($this->client);

        foreach ($paginator->fetchAll($api, 'repositories', ['all']) as $repositoryData) {
            yield new Model\Repository(
                $repositoryData['full_name'],
                $repositoryData['html_url'],
                $repositoryData['private'] ? $repositoryData['clone_url'] : $repositoryData['ssh_url']
            );
        }
    }

    public function setIntegrationStatus(Model\PullRequest $pullRequest, IntegrationStatus $status)
    {
        $this->assertAuthenticated();

        $repository = $pullRequest->getRepository();

        $this->logger->info('Creating integration status for PR {pull_request}', [
            'pull_request' => $pullRequest,
            'head' => $pullRequest->getHead(),
            'description' => $status->getDescription(),
        ]);

        $parameters = [
            'state' => $status->getState(),
            'description' => $status->getDescription(),
            'context' => IntegrationStatus::STATUS_CONTEXT,
        ];

        if ($status->getTargetUrl()) {
            $parameters['target_url'] = $status->getTargetUrl();
        }

        $this->client->repo()->statuses()->create($repository->getOwner(), $repository->getName(), $pullRequest->getHead(), $parameters);
    }

    public function addDeployKey(string $owner, string $repository, string $title, string $key, string $type)
    {
        $this->assertAuthenticated();

        $this->logger->info('Adding new deploy key for repository {repository} -- {key_title}', [
            'repository' => sprintf('%s/%s', $owner, $repository),
            'key_title' => $title,
        ]);

        $this->client->repo()->keys()->create($owner, $repository, [
            'title' => $title,
            'key' => $key,
            'read_only' => $type === self::READONLY_KEY,
        ]);
    }

    public function createWebhook(string $owner, string $repository, string $url, $secret = null)
    {
        $this->assertAuthenticated();

        $this->logger->info('Creating webhook for {repository} to call {url}', [
            'repository' => sprintf('%s/%s', $owner, $repository),
            'url' => $url,
        ]);

        $this->client->repo()->hooks()->create($owner, $repository, [
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
            'pull_request' => $pullRequest->getNumber(),
            'commit_id' => $pullRequest->getHead(),
            'path' => $comment->getFile(),
            'position' => $comment->getPosition(),
            'comment' => $comment->getContent(),
        ]);

        $this->client->pullRequest()->comments()->create($repository->getOwner(), $repository->getName(), $pullRequest->getNumber(), [
            'commit_id' => $pullRequest->getHead(),
            'path' => $comment->getFile(),
            'position' => $comment->getPosition(),
            'body' => $comment->getContent(),
        ]);
    }

    private function assertAuthenticated()
    {
        if (!$this->authenticated) {
            $this->client->authenticate($this->user->getGithubAccessToken(), '', \Github\Client::AUTH_URL_TOKEN);
            $this->authenticated = true;
        }
    }
}
