<?php

declare(strict_types=1);

namespace Regis\Domain\Repository;

use RulerZ\Spec\Specification;

use Regis\Domain\Entity;

interface Teams
{
    public function save(Entity\Team $team);

    public function matching(Specification $spec): \Traversable;

    /**
     * @throws \Regis\Domain\Repository\Exception\NotFound
     */
    public function find(string $id): Entity\Team;
}
