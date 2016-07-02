<?php

declare(strict_types=1);

namespace Regis\Application\Command\Github\Inspection;

use Regis\Domain\Model\Github\PullRequest;

class SchedulePullRequest
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