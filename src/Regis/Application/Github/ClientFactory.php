<?php

declare(strict_types=1);

namespace Regis\Application\Github;

use Regis\Domain\Entity;

interface ClientFactory
{
    function createForRepository(Entity\Github\Repository $repository): Client;
    function createForUser(Entity\User $user): Client;
}
