<?php

declare(strict_types=1);

namespace Regis\AppContext\Domain\Repository;

use Regis\AppContext\Domain\Entity;

interface Users
{
    public function findById(string $id): Entity\User;
}
