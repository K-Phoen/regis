<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\Command;

use RulerZ\Spec\Specification;
use Regis\AppContext\Domain\Entity;

interface SecureCommandBySpecification
{
    public static function executionAuthorizedFor(Entity\User $user): Specification;

    public function getTargetToSecure();
}
