<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application\Inspection;

interface PhpMdRunner
{
    public function execute(string $fileName, string $fileContent, string $ruleset): \Traversable;
}
