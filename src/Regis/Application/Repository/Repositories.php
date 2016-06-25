<?php

namespace Regis\Application\Repository;

use Regis\Application\Entity;

interface Repositories
{
    public function save(Entity\Repository $inspections);

    public function findAll(): \Traversable;
    public function find(string $id): Entity\Repository;
}
