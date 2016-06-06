<?php

declare(strict_types=1);

namespace Regis\Application\Event;

use Regis\Application\Event;
use Regis\Application\Model\Github\PullRequest;

class PullRequestClosed implements Event
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
        return Event::PULL_REQUEST_CLOSED;
    }
}