<?php

declare(strict_types=1);

namespace Regis\AppContext\Domain\Repository;

use RulerZ\Spec\Specification;
use Regis\AppContext\Domain\Entity;

interface Repositories
{
    const MODE_FETCH_NOTHING = 0;
    const MODE_FETCH_RELATIONS = 1;

    public function save(Entity\Repository $team);

    public function matching(Specification $spec): \Traversable;

    /**
     * @throws \Regis\AppContext\Domain\Repository\Exception\NotFound
     */
    public function find(string $id, $mode = self::MODE_FETCH_NOTHING): Entity\Repository;
}
