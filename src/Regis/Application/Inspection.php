<?php

declare(strict_types=1);

namespace Regis\Application;

use Regis\Domain\Model;

interface Inspection
{
    public function getType(): string;

    public function inspectDiff(Model\Git\Diff $diff): \Traversable;
}
