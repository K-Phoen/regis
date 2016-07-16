<?php

declare(strict_types=1);

namespace Regis\Application\Random;

use Regis\Domain\Model;

interface Generator
{
    function randomString(int $length = 24): string;
}