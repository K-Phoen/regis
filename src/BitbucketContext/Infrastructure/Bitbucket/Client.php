<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Infrastructure\Bitbucket;

use Bitbucket\API\Http\Response\Pager;
use Bitbucket\API\Repositories;
use Buzz\Message\MessageInterface;
use Psr\Log\LoggerInterface as Logger;
use Bitbucket\API\Api as VendorClient;
use Regis\BitbucketContext\Application\Bitbucket\BuildStatus;
use Regis\BitbucketContext\Application\Bitbucket\Client as BitbucketClient;
use Regis\BitbucketContext\Domain\Entity\BitbucketDetails;
use Regis\BitbucketContext\Domain\Model;

class Client implements BitbucketClient
{
    private $client;
    private $user;
    private $logger;

    public function __construct(VendorClient $client, BitbucketDetails $user, Logger $logger)
    {
        $this->client = $client;
        $this->user = $user;
        $this->logger = $logger;
    }

    public function listRepositories(): \Traversable
    {
        $this->logger->info('Fetching repositories list for user {owner_id}', [
            'owner_id' => $this->user->accountId(),
        ]);

        /** @var Repositories $repositories */
        $repositories = $this->client->api('Repositories');

        $page = new Pager($repositories->getClient(), $repositories->all($this->user->getUsername()));
        while ($response = $page->fetchNext()) {
            $content = json_decode($response->getContent(), true);

            yield from $this->parseRepositories($content);
        }
    }

    public function addDeployKey(Model\RepositoryIdentifier $repository, string $title, string $key)
    {
        $this->logger->info('Adding new deploy key for repository {repository} -- {key_title}', [
            'owner_id' => $this->user->accountId(),
            'repository' => $repository->value(),
            'key_title' => $title,
        ]);

        /** @var Repositories\Deploykeys $deployKeys */
        $deployKeys = $this->client->api('Repositories\\Deploykeys');
        $response = $deployKeys->create($this->user->getUsername(), $repository->value(), $key, $title);

        // TODO an error is returned by bitbucket if the deploy key already exists
        $this->decodeResponse($response);
    }

    public function createWebhook(Model\RepositoryIdentifier $repository, string $url)
    {
        $this->logger->info('Creating webhook for repository {repository}: {url}', [
            'owner_id' => $this->user->accountId(),
            'repository' => $repository->value(),
            'url' => $url,
        ]);

        /** @var Repositories\Hooks $hooks */
        $hooks = $this->client->api('Repositories\\Hooks');
        $response = $hooks->create($this->user->getUsername(), $repository->value(), [
            'description' => 'Regis webhook',
            'url' => $url,
            'active' => true,
            'events' => [
                'pullrequest:created',
                'pullrequest:updated',
                'pullrequest:rejected',
                'pullrequest:fulfilled',
            ],
        ]);

        // TODO an error is returned by bitbucket if the deploy key already exists
        $this->decodeResponse($response);
    }

    public function sendComment(Model\PullRequest $pullRequest, Model\ReviewComment $comment)
    {
        $this->logger->info('Sending review comment for PR {pull_request} -- {commit_id}@{path}:{position} -- {comment}', [
            'owner_id' => $this->user->accountId(),
            'repository_id' => $pullRequest->getRepository()->value(),
            'pull_request' => $pullRequest->getNumber(),
            'commit_id' => $pullRequest->getHead(),
            'path' => $comment->file(),
            'line' => $comment->line(),
            'comment' => $comment->content(),
        ]);

        $this->client->getClient()->setApiVersion('1.0')->post(
            sprintf('repositories/%s/%s/pullrequests/%d/comments/', $this->user->getUsername(), $pullRequest->getRepository()->value(), $pullRequest->getNumber()),
            [
                'anchor' => $pullRequest->getHead(),
                'dest_rev' => $pullRequest->getBase(),
                'line_to' => $comment->line(),
                'filename' => $comment->file(),
                'content' => $comment->content(),
            ]
        );
    }

    public function getCloneUrl(Model\RepositoryIdentifier $repository): string
    {
        $this->logger->info('Finding clone URL for repository {repository_id}', [
            'repository_id' => $repository->value(),
            'owner_id' => $this->user->accountId(),
        ]);

        /** @var Repositories\Repository $repositories */
        $repositories = $this->client->api('Repositories\\Repository');
        $response = $repositories->get($this->user->getUsername(), $repository->value());

        $decodedResponse = json_decode($response->getContent(), true);
        $repositoryModel = $this->hydrateRepository($decodedResponse);

        return $repositoryModel->getCloneUrl();
    }

    public function getPullRequest(Model\RepositoryIdentifier $repository, int $number): Model\PullRequest
    {
        $this->logger->info('Fetching pull request {number} for repository {repository_id}', [
            'repository_id' => $repository->value(),
            'number' => $number,
            'owner_id' => $this->user->accountId(),
        ]);

        /** @var Repositories\PullRequests $pullRequests */
        $pullRequests = $this->client->api('Repositories\\PullRequests');

        $response = $pullRequests->get($this->user->getUsername(), $repository->value(), $number);
        $decodedResponse = json_decode($response->getContent(), true);

        return new Model\PullRequest(
            $repository,
            $number,
            $decodedResponse['source']['commit']['hash'],
            $decodedResponse['destination']['commit']['hash']
        );
    }

    public function setBuildStatus(Model\RepositoryIdentifier $repository, BuildStatus $status, string $revision)
    {
        $this->logger->info('Setting build status to {state} for repository {repository}', [
            'repository' => $repository->value(),
            'head' => $revision,
            'description' => $status->description(),
            'state' => $status->state(),
        ]);

        $parameters = [
            'state' => $status->state(),
            'key' => sha1($status->key()), // a valid key has at most 40 characters
            'name' => 'Regis',
            'url' => $status->url(),
            'description' => $status->description(),
        ];

        /** @var Repositories\Commits\BuildStatuses $buildStatuses */
        $buildStatuses = $this->client->api('Repositories\\Commits\\BuildStatuses');

        $buildStatuses->create($this->user->getUsername(), $repository->value(), $revision, $parameters);
    }

    private function parseRepositories(array $response)
    {
        foreach ($response['values'] as $item) {
            if ($item['scm'] !== 'git') {
                continue;
            }

            yield $this->hydrateRepository($item);
        }
    }

    private function hydrateRepository(array $data)
    {
        $cloneEndpoint = array_filter($data['links']['clone'], function (array $endpoint) {
            return $endpoint['name'] === 'ssh';
        });

        return new Model\Repository(
            new Model\RepositoryIdentifier($data['uuid']),
            $data['name'],
            current($cloneEndpoint)['href'],
            $data['links']['html']['href']
        );
    }

    private function decodeResponse(MessageInterface $response): array
    {
        $payload = $response->getContent();
        $decodedResponse = json_decode($payload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(sprintf("Error while decoding Bitbucket response: \"%s\". Payload:\n%s", json_last_error_msg(), $payload));
        }

        if (isset($decodedResponse['type']) && $decodedResponse['type'] === 'error') {
            throw new \RuntimeException(sprintf('Bitbucket client error: "%s"', $decodedResponse['error']['message']));
        }

        return $decodedResponse;
    }
}
