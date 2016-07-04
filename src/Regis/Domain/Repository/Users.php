<?php

namespace Regis\Domain\Repository;

use Regis\Domain\Entity;

interface Users
{
    public function save(Entity\User $user);

    public function findByGithubId(int $id): Entity\User;
    public function findById(int $id): Entity\User;
}
