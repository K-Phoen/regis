<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\Bitbucket;

use Regis\BitbucketContext\Domain\Model;

interface Client
{
    public function listRepositories(): \Traversable;

    public function getCloneUrl(Model\RepositoryIdentifier $repository): string;

    public function getPullRequest(Model\RepositoryIdentifier $repository, int $number): Model\PullRequest;

    public function setBuildStatus(Model\RepositoryIdentifier $repository, BuildStatus $status, string $revision);

    public function sendComment(Model\PullRequest $pullRequest, Model\ReviewComment $comment);

    public function addDeployKey(Model\RepositoryIdentifier $repository, string $title, string $key);

    public function createWebhook(Model\RepositoryIdentifier $repository, string $url);
}
