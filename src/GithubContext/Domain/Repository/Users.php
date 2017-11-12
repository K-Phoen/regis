<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Repository;

use RulerZ\Spec\Specification;

use Regis\GithubContext\Domain\Entity;

interface Users
{
    public function save(Entity\User $user);

    public function findByGithubId(int $id): Entity\User;

    public function findById(string $id): Entity\User;

    public function matching(Specification $spec): \Traversable;
}
