<?php

declare(strict_types=1);

namespace Regis\Application\Command\Github\Inspection;

use Regis\Application\Entity\Github\PullRequestInspection;
use Regis\Application\Model\Github\PullRequest;

class Run
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