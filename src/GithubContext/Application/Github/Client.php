<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Github;

use Regis\GithubContext\Domain\Model;

interface Client
{
    const INTEGRATION_PENDING = 'pending';
    const INTEGRATION_SUCCESS = 'success';
    const INTEGRATION_FAILURE = 'failure';
    const INTEGRATION_ERROR = 'error';

    const READONLY_KEY = 'readonly_key';
    const WRITE_KEY = 'write_key';

    public function setIntegrationStatus(Model\RepositoryIdentifier $repository, string $head, IntegrationStatus $status);

    public function addDeployKey(Model\RepositoryIdentifier $repository, string $title, string $key, string $type);

    public function createWebhook(Model\RepositoryIdentifier $repository, string $url, $secret = null);

    public function sendComment(Model\PullRequest $pullRequest, Model\ReviewComment $comment);

    public function listRepositories(): \Traversable;

    public function getPullRequestDetails(Model\RepositoryIdentifier $repository, int $number): array;
}
