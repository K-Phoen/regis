<?php

declare(strict_types=1);

namespace Regis\Infrastructure\RandomLib;

use Regis\Application\Random\Generator as RandomGenerator;

class Generator implements RandomGenerator
{
    public function randomString(int $length = 24): string
    {
        return random_bytes($length);
    }
}
