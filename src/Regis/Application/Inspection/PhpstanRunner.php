<?php

declare(strict_types=1);

namespace Regis\Application\Inspection;

interface PhpstanRunner
{
    public function execute(string $fileName): \Traversable;
}
