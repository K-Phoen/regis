<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\Command;

use Regis\AppContext\Domain\Entity;

interface SecureCommand
{
    public function executionAuthorizedFor(Entity\User $user): bool;
}
