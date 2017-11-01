<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Command\Inspection;

use Regis\GithubContext\Domain\Model;

class SchedulePullRequest
{
    private $pullRequest;

    public function __construct(Model\PullRequest $pullRequest)
    {
        $this->pullRequest = $pullRequest;
    }

    public function getPullRequest(): Model\PullRequest
    {
        return $this->pullRequest;
    }
}
