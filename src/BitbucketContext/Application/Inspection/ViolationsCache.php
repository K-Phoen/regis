<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\Inspection;

use Predis\ClientInterface;
use Regis\BitbucketContext\Domain\Model;

class ViolationsCache
{
    private $redis;

    public function __construct(ClientInterface $redis)
    {
        $this->redis = $redis;
    }

    public function has(Model\ReviewComment $comment, Model\PullRequest $pullRequest): bool
    {
        return (bool) $this->redis->sismember($this->getPullRequestKey($pullRequest), $this->getViolationKey($comment));
    }

    public function save(Model\ReviewComment $comment, Model\PullRequest $pullRequest)
    {
        $this->redis->sadd($this->getPullRequestKey($pullRequest), [$this->getViolationKey($comment)]);
    }

    public function clear(Model\PullRequest $pullRequest)
    {
        $this->redis->del([$this->getPullRequestKey($pullRequest)]);
    }

    private function getPullRequestKey(Model\PullRequest $pullRequest)
    {
        return sprintf('%s#%d', $pullRequest->getRepository()->value(), $pullRequest->getNumber());
    }

    private function getViolationKey(Model\ReviewComment $comment)
    {
        return md5(
            sprintf('%s:%d -- %s', $comment->file(), $comment->line(), $comment->content())
        );
    }
}
