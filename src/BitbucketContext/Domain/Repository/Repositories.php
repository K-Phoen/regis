<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Repository;

use Regis\BitbucketContext\Domain\Entity;

interface Repositories
{
    public function save(Entity\Repository $team);

    /**
     * @throws \Regis\BitbucketContext\Domain\Repository\Exception\NotFound
     */
    public function find(string $id): Entity\Repository;
}
