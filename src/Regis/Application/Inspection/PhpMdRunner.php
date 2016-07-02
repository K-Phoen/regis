<?php

declare(strict_types=1);

namespace Regis\Application\Inspection;

interface PhpMdRunner
{
    function execute(string $fileName, string $fileContent): \Traversable;
}