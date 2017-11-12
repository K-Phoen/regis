<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application\Inspection;

interface PhpstanRunner
{
    public function execute(string $fileName): \Traversable;
}
