<?php

namespace Regis\Domain\Event;

use Regis\Domain\Model\Github\PullRequest;

class PullRequestOpened
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
}