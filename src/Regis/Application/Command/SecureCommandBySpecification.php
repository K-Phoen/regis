<?php

declare(strict_types = 1);

namespace Regis\Application\Command;

use RulerZ\Spec\Specification;

use Regis\Domain\Entity;

interface SecureCommandBySpecification
{
    /**
     * @param Entity\User $user
     *
     * @return Specification
     */
    function executionAuthorizedFor(Entity\User $user): Specification;

    function getTargetToSecure();
}