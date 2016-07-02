<?php

namespace Regis\Application\Repository;

use Regis\Domain\Entity;

interface Inspections
{
    public function save(Entity\Inspection $inspection);

    public function find(string $id): Entity\Inspection;
}
