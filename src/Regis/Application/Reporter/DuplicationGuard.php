<?php

declare(strict_types=1);

namespace Regis\Application\Reporter;

use Regis\Application\Inspection\ViolationsCache;
use Regis\Application\Model;
use Regis\Application\Reporter;

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