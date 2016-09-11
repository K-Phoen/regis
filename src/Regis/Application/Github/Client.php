<?php

declare(strict_types=1);

namespace Regis\Application\Github;

use Regis\Domain\Model\Github as Model;

interface Client
{
    const INTEGRATION_PENDING = 'pending';
    const INTEGRATION_SUCCESS = 'success';
    const INTEGRATION_FAILURE = 'failure';
    const INTEGRATION_ERROR = 'error';

    const READONLY_KEY = 'readonly_key';
    const WRITE_KEY = 'write_key';

    function setIntegrationStatus(Model\PullRequest $pullRequest, IntegrationStatus $status);
    function addDeployKey(string $owner, string $repository, string $title, string $key, string $type);
    function createWebhook(string $owner, string $repository, string $url, $secret = null);
    function sendComment(Model\PullRequest $pullRequest, Model\ReviewComment $comment);
    function listRepositories(): \Traversable;
}