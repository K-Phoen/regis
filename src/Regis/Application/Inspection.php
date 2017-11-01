<?php

declare(strict_types=1);

namespace Regis\Application;

use Regis\Domain\Model;
use Regis\Application\Vcs;

interface Inspection
{
    public function getType(): string;

    public function inspectDiff(Vcs\Repository $repository, Model\Git\Diff $diff): \Traversable;
}
