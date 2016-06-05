<?php

declare(strict_types=1);

namespace Regis\Domain\Reporter;

use Predis\ClientInterface;
use Regis\Domain\Model;
use Regis\Domain\Reporter;

class DuplicationGuard implements Reporter
{
    private $originalReporter;
    private $redis;

    public function __construct(Reporter $originalReporter, ClientInterface $redis)
    {
        $this->originalReporter = $originalReporter;
        $this->redis = $redis;
    }

    public function report(Model\Violation $violation, Model\Github\PullRequest $pullRequest)
    {
        $setKey = (string) $pullRequest;
        $violationKey = (string) $violation;

        if ($this->redis->sismember($setKey, $violationKey)) {
            return;
        }

        $this->originalReporter->report($violation, $pullRequest);

        $this->redis->sadd($setKey, $violationKey);
    }
}