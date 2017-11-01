<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application\Inspection;

interface CodeSnifferRunner
{
    public function execute(string $fileName, string $fileContent): array;
}
