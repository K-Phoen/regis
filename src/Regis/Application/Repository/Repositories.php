<?php

namespace Regis\Application\Repository;

use Regis\Application\Entity;

interface Repositories
{
    public function save(Entity\Repository $repository);

    public function findAll(): \Traversable;
    public function find(string $identifier): Entity\Repository;
}
