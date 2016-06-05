<?php

declare(strict_types=1);

namespace Regis\Domain\Inspection;

use Predis\ClientInterface;
use Regis\Domain\Model;

class ViolationsCache
{
    private $redis;

    public function __construct(ClientInterface $redis)
    {
        $this->redis = $redis;
    }

    public function has(Model\Violation $violation, Model\Github\PullRequest $pullRequest): bool
    {
        return (bool) $this->redis->sismember($this->getPullRequestKey($pullRequest), $this->getViolationKey($violation));
    }

    public function save(Model\Violation $violation, Model\Github\PullRequest $pullRequest)
    {
        $this->redis->sadd($this->getPullRequestKey($pullRequest), $this->getViolationKey($violation));
    }

    public function clear(Model\Github\PullRequest $pullRequest)
    {
        $this->redis->del($this->getPullRequestKey($pullRequest));
    }

    private function getPullRequestKey(Model\Github\PullRequest $pullRequest)
    {
        return (string) $pullRequest;
    }

    private function getViolationKey(Model\Violation $violation)
    {
        return (string) $violation;
    }
}