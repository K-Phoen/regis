<?php

declare(strict_types=1);

namespace Regis\Kernel\Security\Exception;

class AccessDenied extends \RuntimeException
{
    public static function forCommand($command): self
    {
        return new static(sprintf('Execution of command "%s" denied.', get_class($command)));
    }
}
