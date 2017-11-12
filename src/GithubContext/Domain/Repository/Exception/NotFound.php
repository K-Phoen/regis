<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Repository\Exception;

class NotFound extends \RuntimeException
{
    public static function forIdentifier(string $identifier): self
    {
        return new static(sprintf('Entity not found for identifier "%s"', $identifier));
    }
}
