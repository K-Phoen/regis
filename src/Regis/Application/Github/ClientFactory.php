<?php

declare(strict_types=1);

namespace Regis\Application\Github;

use Regis\Domain\Entity;

interface ClientFactory
{
    public function createForRepository(Entity\Github\Repository $repository): Client;

    public function createForUser(Entity\User $user): Client;
}
