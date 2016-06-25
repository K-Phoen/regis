<?php

namespace Regis\Application\Repository;

use Regis\Application\Entity;

interface Inspections
{
    public function save(Entity\Inspection $inspection);

    public function find(string $id): Entity\Inspection;
}
