<?php

declare(strict_types=1);

namespace Regis\Application\Github;

use Regis\Domain\Model\Github as Model;

interface Client
{
    function setIntegrationStatus(Model\PullRequest $pullRequest, string $state, string $description, string $context);
    function addDeployKey(string $owner, string $repository, string $title, string $key, string $type);
    function createWebhook(string $owner, string $repository, string $url, $secret = null);
    function sendComment(Model\PullRequest $pullRequest, Model\ReviewComment $comment);
}