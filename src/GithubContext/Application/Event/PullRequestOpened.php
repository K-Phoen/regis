<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Event;

use Regis\Event\Events;
use Regis\GithubContext\Domain\Model\PullRequest;

class PullRequestOpened implements Events
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
        return Events::PULL_REQUEST_OPENED;
    }
}
