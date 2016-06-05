<?php

declare(strict_types=1);

namespace Regis\Domain\Reporter;

use Regis\Domain\Inspection\ViolationsCache;
use Regis\Domain\Model;
use Regis\Domain\Reporter;

class DuplicationGuard implements Reporter
{
    private $originalReporter;
    private $violationsCache;

    public function __construct(Reporter $originalReporter, ViolationsCache $violationsCache)
    {
        $this->originalReporter = $originalReporter;
        $this->violationsCache = $violationsCache;
    }

    public function report(Model\Violation $violation, Model\Github\PullRequest $pullRequest)
    {
        if ($this->violationsCache->has($violation, $pullRequest)) {
            return;
        }

        $this->originalReporter->report($violation, $pullRequest);

        $this->violationsCache->save($violation, $pullRequest);
    }
}