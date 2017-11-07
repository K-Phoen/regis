<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Random;

class Generator
{
    public function randomString(int $length = 24): string
    {
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }
}
