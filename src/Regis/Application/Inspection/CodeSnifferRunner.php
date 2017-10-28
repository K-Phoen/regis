<?php

declare(strict_types=1);

namespace Regis\Application\Inspection;

interface CodeSnifferRunner
{
    public function execute(string $fileName, string $fileContent): array;
}
