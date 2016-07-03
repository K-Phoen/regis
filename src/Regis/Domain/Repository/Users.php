<?php

namespace Regis\Domain\Repository;

use Regis\Domain\Entity;

interface Users
{
    public function save(Entity\User $user);
}
