<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Command\Inspection;

use Regis\GithubContext\Domain\Entity;

class SavePullRequestReport
{
    private $inspection;
    private $report;

    public function __construct(Entity\PullRequestInspection $inspection, Entity\Inspection\Report $report)
    {
        $this->inspection = $inspection;
        $this->report = $report;
    }

    public function getInspection(): Entity\PullRequestInspection
    {
        return $this->inspection;
    }

    public function getReport(): Entity\Inspection\Report
    {
        return $this->report;
    }
}
