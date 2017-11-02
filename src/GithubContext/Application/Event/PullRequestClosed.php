<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Event;

use Regis\GithubContext\Domain\Model\PullRequest;
use Regis\GithubContext\Application\Events as GithubEvents;
use Regis\Kernel\Events;

class PullRequestClosed implements Events
{
    private $pullRequest;

    public function __construct(PullRequest $pullRequest)
    {
        $this->pullRequest = $pullRequest;
    }

    public function getPullRequest(): PullRequest
    {
        return $this->pullRequest;
    }

    public function getEventName(): string
    {
        return GithubEvents::PULL_REQUEST_CLOSED;
    }
}
