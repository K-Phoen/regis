<?php

namespace Regis\Application\Repository;

use Regis\Domain\Entity;

interface Repositories
{
    public function save(Entity\Repository $repository);

    public function findAll(): \Traversable;
    public function find(string $id): Entity\Repository;
}
