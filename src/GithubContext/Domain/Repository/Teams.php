<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Repository;

use RulerZ\Spec\Specification;

use Regis\GithubContext\Domain\Entity;

interface Teams
{
    public function save(Entity\Team $team);

    public function matching(Specification $spec): \Traversable;

    /**
     * @throws Exception\NotFound
     */
    public function find(string $id): Entity\Team;
}
