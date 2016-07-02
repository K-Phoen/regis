<?php

namespace Regis\Domain\Repository\Exception;

class NotFound extends \RuntimeException
{
    public static function forIdentifier(string $identifier): NotFound
    {
        return new static(sprintf('Entity not found for identifier "%s"', $identifier));
    }
}
