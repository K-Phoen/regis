<?php

declare(strict_types=1);

namespace Regis\Application\Inspection;

interface CodeSnifferRunner
{
    function execute(string $fileName, string $fileContent): array;
}