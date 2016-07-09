<?php

declare(strict_types=1);

namespace Regis\Application\Reporter;

use Regis\Application\Inspection\ViolationsCache;
use Regis\Application\Reporter;
use Regis\Domain\Entity;
use Regis\Domain\Model;

class DuplicationGuard implements Reporter
{
    private $originalReporter;
    private $violationsCache;

    public function __construct(Reporter $originalReporter, ViolationsCache $violationsCache)
    {
        $this->originalReporter = $originalReporter;
        $this->violationsCache = $violationsCache;
    }

    public function report(Entity\Repository $repository, Entity\Inspection\Violation $violation, Model\Github\PullRequest $pullRequest)
    {
        if ($this->violationsCache->has($violation, $pullRequest)) {
            return;
        }

        $this->originalReporter->report($repository, $violation, $pullRequest);

        $this->violationsCache->save($violation, $pullRequest);
    }
}
