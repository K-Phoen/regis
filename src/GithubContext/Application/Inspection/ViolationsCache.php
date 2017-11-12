<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Inspection;

use Predis\ClientInterface;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Model;

class ViolationsCache
{
    private $redis;

    public function __construct(ClientInterface $redis)
    {
        $this->redis = $redis;
    }

    public function has(Entity\Violation $violation, Model\PullRequest $pullRequest): bool
    {
        return (bool) $this->redis->sismember($this->getPullRequestKey($pullRequest), $this->getViolationKey($violation));
    }

    public function save(Entity\Violation $violation, Model\PullRequest $pullRequest)
    {
        $this->redis->sadd($this->getPullRequestKey($pullRequest), $this->getViolationKey($violation));
    }

    public function clear(Model\PullRequest $pullRequest)
    {
        $this->redis->del($this->getPullRequestKey($pullRequest));
    }

    private function getPullRequestKey(Model\PullRequest $pullRequest)
    {
        return (string) $pullRequest;
    }

    private function getViolationKey(Entity\Violation $violation)
    {
        return md5((string) $violation);
    }
}
