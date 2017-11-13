<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Repository;

use Regis\BitbucketContext\Domain\Entity;

interface Users
{
    public function save(Entity\User $user);

    public function findByBitbucketId(string $id): Entity\User;

    public function findById(string $id): Entity\User;
}
