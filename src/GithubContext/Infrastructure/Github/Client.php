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

namespace Regis\GithubContext\Infrastructure\Github;

use Psr\Log\LoggerInterface as Logger;
use Regis\GithubContext\Application\Github\IntegrationStatus;
use Regis\GithubContext\Application\Github\Client as GithubClient;
use Regis\GithubContext\Domain\Entity\GithubDetails;
use Regis\GithubContext\Domain\Model;

class Client implements GithubClient
{
    private $client;
    private $user;
    private $logger;
    private $authenticated = false;

    public function __construct(\Github\Client $client, GithubDetails $user, Logger $logger)
    {
        $this->client = $client;
        $this->user = $user;
        $this->logger = $logger;
    }

    public function listRepositories(): \Traversable
    {
        $this->assertAuthenticated();

        $this->logger->info('Fetching repositories list for user {id}', [
            'id' => $this->user->accountId(),
        ]);
        $api = $this->client->currentUser();
        $paginator = new \Github\ResultPager($this->client);

        foreach ($paginator->fetchAll($api, 'repositories', ['all']) as $repositoryData) {
            yield new Model\Repository(
                Model\RepositoryIdentifier::fromFullName($repositoryData['full_name']),
                $repositoryData['html_url'],
                $repositoryData['private'] ? $repositoryData['clone_url'] : $repositoryData['ssh_url']
            );
        }
    }

    public function getPullRequestDetails(Model\RepositoryIdentifier $repository, int $number): array
    {
        $this->assertAuthenticated();

        $this->logger->info('Fetching pull request {pull_request}', [
            'repository_owner_id' => $this->user->accountId(),
            'pull_request' => $number,
            'repository' => $repository->getIdentifier(),
        ]);

        return $this->client->pullRequest()->show($repository->getOwner(), $repository->getName(), $number);
    }

    public function setIntegrationStatus(Model\RepositoryIdentifier $repository, string $head, IntegrationStatus $status)
    {
        $this->assertAuthenticated();
        $this->logger->info('Creating integration status for repository {repository_identifer}', [
            'repository_identifier' => $head,
            'head' => $head,
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

        $this->client->repo()->statuses()->create($repository->getOwner(), $repository->getName(), $head, $parameters);
    }

    public function addDeployKey(Model\RepositoryIdentifier $repository, string $title, string $key, string $type)
    {
        $this->assertAuthenticated();

        $this->logger->info('Adding new deploy key for repository {repository} -- {key_title}', [
            'repository' => $repository->getIdentifier(),
            'key_title' => $title,
        ]);

        $this->client->repo()->keys()->create($repository->getOwner(), $repository->getName(), [
            'title' => $title,
            'key' => $key,
            'read_only' => $type === self::READONLY_KEY,
        ]);
    }

    public function createWebhook(Model\RepositoryIdentifier $repository, string $url, $secret = null)
    {
        $this->assertAuthenticated();

        $this->logger->info('Creating webhook for {repository} to call {url}', [
            'repository' => $repository->getIdentifier(),
            'url' => $url,
        ]);

        $this->client->repo()->hooks()->create($repository->getOwner(), $repository->getName(), [
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

        $repository = $pullRequest->getRepositoryIdentifier();

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
            $this->client->authenticate($this->user->getAccessToken(), '', \Github\Client::AUTH_URL_TOKEN);
            $this->authenticated = true;
        }
    }
}
