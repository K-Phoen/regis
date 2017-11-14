<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\Event;

use Regis\BitbucketContext\Domain\Model\PullRequest;

class PullRequestUpdated
{
    private $pullRequest;
    private $before;
    private $after;

    public function __construct(PullRequest $pullRequest, string $before, string $after)
    {
        $this->pullRequest = $pullRequest;
        $this->before = $before;
        $this->after = $after;
    }

    public function getPullRequest(): PullRequest
    {
        return $this->pullRequest;
    }

    public function getBefore(): string
    {
        return $this->before;
    }

    public function getAfter(): string
    {
        return $this->after;
    }
}