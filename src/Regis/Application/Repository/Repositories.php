<?php

namespace Regis\Application\Repository;

use Regis\Application\Model;

interface Repositories
{
    public function save(Model\Repository $repository);

    public function findAll(): \Traversable;
    public function find(string $identifier): Model\Repository;
}
