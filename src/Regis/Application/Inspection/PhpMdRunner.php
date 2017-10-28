<?php

declare(strict_types=1);

namespace Regis\Application\Inspection;

interface PhpMdRunner
{
    public function execute(string $fileName, string $fileContent): \Traversable;
}
