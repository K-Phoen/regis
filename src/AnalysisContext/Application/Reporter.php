<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application;

use Regis\Domain\Entity;
use Regis\Domain\Model;

interface Reporter
{
    public function report(Entity\Repository $repository, Entity\Inspection\Violation $violation, Model\Github\PullRequest $pullRequest);
}
