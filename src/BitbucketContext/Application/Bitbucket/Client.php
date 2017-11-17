<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\Bitbucket;

use Regis\BitbucketContext\Domain\Model;

interface Client
{
    public function listRepositories(): \Traversable;

    public function getCloneUrl(Model\RepositoryIdentifier $repository): string;

    public function getPullRequest(Model\RepositoryIdentifier $repository, int $number): Model\PullRequest;
}
