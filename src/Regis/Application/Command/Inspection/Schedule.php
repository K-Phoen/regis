<?php

declare(strict_types=1);

namespace Regis\Application\Command\Inspection;

use Regis\Application\Model\Github\PullRequest;

class Schedule
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