<?php

declare(strict_types=1);

namespace Regis\Application\Command\Github\Inspection;

use Regis\Domain\Entity\Github\PullRequestInspection;
use Regis\Domain\Model\Github\PullRequest;

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
