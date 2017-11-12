<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Github;

use Regis\GithubContext\Domain\Entity;

interface ClientFactory
{
    public function createForRepository(Entity\Repository $repository): Client;

    public function createForUser(Entity\User $user): Client;
}
