<?php

namespace Regis\Domain\Repository;

use Regis\Domain\Entity;

interface Repositories
{
    public function save(Entity\Repository $repository);

    public function findForUser(Entity\User $user): \Traversable;
    public function find(string $id): Entity\Repository;
}
