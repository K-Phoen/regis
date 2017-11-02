<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Event;

use Regis\GithubContext\Domain\Model\PullRequest;
use Regis\GithubContext\Application\Events as GithubEvents;
use Regis\Kernel\Events;

class PullRequestSynced implements Events
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

    public function getEventName(): string
    {
        return GithubEvents::PULL_REQUEST_SYNCED;
    }
}
