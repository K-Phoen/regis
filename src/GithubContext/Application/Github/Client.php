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

    public function setIntegrationStatus(Model\PullRequest $pullRequest, IntegrationStatus $status);

    public function addDeployKey(string $owner, string $repository, string $title, string $key, string $type);

    public function createWebhook(string $owner, string $repository, string $url, $secret = null);

    public function sendComment(Model\PullRequest $pullRequest, Model\ReviewComment $comment);

    public function listRepositories(): \Traversable;
}
