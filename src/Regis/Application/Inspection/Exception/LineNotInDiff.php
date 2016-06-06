<?php

declare(strict_types=1);

namespace Regis\Application\Inspection\Exception;

class LineNotInDiff extends \RuntimeException
{
    public static function line(int $line): LineNotInDiff
    {
        return new static(sprintf('Line %d is not in diff.', $line));
    }
}