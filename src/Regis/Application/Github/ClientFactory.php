<?php

declare(strict_types=1);

namespace Regis\Application\Github;

use Regis\Domain\Entity;

interface ClientFactory
{
    function createForUser(Entity\User $user): Client;
    function createForRepository(Entity\Github\Repository $repository): Client;
}
