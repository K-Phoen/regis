<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Command\Inspection;

use Regis\GithubContext\Domain\Entity\PullRequestInspection;
use Regis\GithubContext\Domain\Model\PullRequest;

class InspectPullRequest
{
    private $inspection;
    private $pullRequest;

    public function __construct(PullRequestInspection $inspection, PullRequest $pullRequest)
    {
        $this->inspection = $inspection;
        $this->pullRequest = $pullRequest;
    }

    public function getInspection(): PullRequestInspection
    {
        return $this->inspection;
    }

    public function getPullRequest(): PullRequest
    {
        return $this->pullRequest;
    }
}
