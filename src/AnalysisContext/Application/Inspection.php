<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application;

use Regis\AnalysisContext\Domain\Model;

interface Inspection
{
    public function getType(): string;

    public function inspectDiff(Vcs\Repository $repository, Model\Git\Diff $diff): \Traversable;
}
