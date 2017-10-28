<?php

declare(strict_types=1);

namespace Tests\Stub;

use RulerZ\Spec\Specification;

use Regis\Application\Command\SecureCommandBySpecification;
use Regis\Domain\Entity;

class CommandSecureBySpecification implements SecureCommandBySpecification
{
    public static $spec;
    public static $target;

    public function __construct(Specification $spec = null, $target = null)
    {
        self::$spec = $spec ?: self::$spec;
        self::$target = $target ?: self::$target;
    }

    public static function executionAuthorizedFor(Entity\User $user): Specification
    {
        return self::$spec;
    }

    public function getTargetToSecure()
    {
        return self::$target;
    }
}
