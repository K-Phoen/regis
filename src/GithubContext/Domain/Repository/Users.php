<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Repository;

use RulerZ\Spec\Specification;

use Regis\GithubContext\Domain\Entity;

interface Users
{
    public function save(Entity\GithubDetails $user);

    public function findByGithubId(int $id): Entity\GithubDetails;
    public function findByAccountId(string $id): Entity\GithubDetails;

    public function findById(string $id): Entity\User;

    public function matching(Specification $spec): \Traversable;
}
