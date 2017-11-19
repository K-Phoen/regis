<?php

declare(strict_types=1);

namespace Regis\AppContext\Domain\Repository;

use RulerZ\Spec\Specification;
use Regis\AppContext\Domain\Entity;

interface Teams
{
    public function save(Entity\Team $team);

    public function matching(Specification $spec): \Traversable;

    /**
     * @throws Exception\NotFound
     */
    public function find(string $id): Entity\Team;
}
