<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Domain\Repository;

use Regis\AnalysisContext\Domain\Entity;

interface Inspections
{
    public function save(Entity\Inspection $inspection);

    public function find(string $id): Entity\Inspection;
}
