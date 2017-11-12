<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Repository;

use Regis\GithubContext\Domain\Entity;

interface PullRequestInspections
{
    public function save(Entity\PullRequestInspection $inspection);

    public function find(string $id): Entity\PullRequestInspection;
}
