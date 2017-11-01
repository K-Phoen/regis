<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Command\Inspection;

use Regis\GithubContext\Domain\Model\PullRequest;

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
