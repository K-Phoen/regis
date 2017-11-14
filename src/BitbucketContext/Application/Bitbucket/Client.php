<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\Bitbucket;

interface Client
{
    public function listRepositories(): \Traversable;
}
