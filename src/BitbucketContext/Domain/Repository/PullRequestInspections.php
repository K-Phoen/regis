<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Repository;

use Regis\BitbucketContext\Domain\Entity;

interface PullRequestInspections
{
    public function save(Entity\PullRequestInspection $inspection);

    public function find(string $id): Entity\PullRequestInspection;
}
