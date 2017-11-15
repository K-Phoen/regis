<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\Bitbucket;

use Regis\BitbucketContext\Domain\Model\RepositoryIdentifier;

interface Client
{
    public function listRepositories(): \Traversable;

    public function getCloneUrl(RepositoryIdentifier $repository): string;
}
