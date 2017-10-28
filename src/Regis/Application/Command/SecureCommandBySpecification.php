<?php

declare(strict_types=1);

namespace Regis\Application\Command;

use RulerZ\Spec\Specification;

use Regis\Domain\Entity;

interface SecureCommandBySpecification
{
    public static function executionAuthorizedFor(Entity\User $user): Specification;

    public function getTargetToSecure();
}
