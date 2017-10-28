<?php

declare(strict_types=1);

namespace Regis\Application\Random;

interface Generator
{
    public function randomString(int $length = 24): string;
}
