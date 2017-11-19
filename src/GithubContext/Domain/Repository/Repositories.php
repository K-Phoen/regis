<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Repository;

use Regis\GithubContext\Domain\Entity;

interface Repositories
{
    public function save(Entity\Repository $team);

    /**
     * @throws \Regis\GithubContext\Domain\Repository\Exception\NotFound
     */
    public function find(string $id): Entity\Repository;
}
