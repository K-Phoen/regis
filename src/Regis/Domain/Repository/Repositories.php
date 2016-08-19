<?php

declare(strict_types=1);

namespace Regis\Domain\Repository;

use Regis\Domain\Entity;

interface Repositories
{
    public function save(Entity\Repository $team);

    public function findForUser(Entity\User $user): \Traversable;

    /**
     * @throws \Regis\Domain\Repository\Exception\NotFound
     */
    public function find(string $id): Entity\Repository;
}
