<?php

declare(strict_types=1);

namespace Regis\Application\Command\Github\Inspection;

use Regis\Application\Entity;
use Regis\Application\Model;

class SavePullRequestReport
{
    private $inspection;
    private $pullRequest;
    private $report;

    public function __construct(Entity\Github\PullRequestInspection $inspection, Model\Github\PullRequest $pullRequest, Entity\Inspection\Report $report)
    {
        $this->inspection = $inspection;
        $this->pullRequest = $pullRequest;
        $this->report = $report;
    }

    public function getInspection(): Entity\Github\PullRequestInspection
    {
        return $this->inspection;
    }

    public function getReport(): Entity\Inspection\Report
    {
        return $this->report;
    }

    public function getPullRequest(): Model\Github\PullRequest
    {
        return $this->pullRequest;
    }
}