<?php

declare(strict_types=1);

namespace Regis\Application\Inspection;

use Predis\ClientInterface;
use Regis\Application\Entity;
use Regis\Application\Model;

class ViolationsCache
{
    private $redis;

    public function __construct(ClientInterface $redis)
    {
        $this->redis = $redis;
    }

    public function has(Entity\Inspection\Violation $violation, Model\Github\PullRequest $pullRequest): bool
    {
        return (bool) $this->redis->sismember($this->getPullRequestKey($pullRequest), $this->getViolationKey($violation));
    }

    public function save(Entity\Inspection\Violation $violation, Model\Github\PullRequest $pullRequest)
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

    private function getViolationKey(Entity\Inspection\Violation $violation)
    {
        return md5((string) $violation);
    }
}