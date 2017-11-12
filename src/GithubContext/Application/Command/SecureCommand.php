<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Command;

use Regis\GithubContext\Domain\Entity;

interface SecureCommand
{
    public function executionAuthorizedFor(Entity\User $user): bool;
}
