<?php

declare(strict_types=1);

namespace Regis\Domain\Inspection\Exception;

class LineNotInDiff
{
    public static function line(int $line)
    {
        return new static(sprintf('Line %d is not in diff.', $line));
    }
}