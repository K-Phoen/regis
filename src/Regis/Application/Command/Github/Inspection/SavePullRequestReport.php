<?php

declare(strict_types=1);

namespace Regis\Application\Command\Github\Inspection;

use Regis\Application\Entity;

class SavePullRequestReport
{
    private $inspection;
    private $report;

    public function __construct(Entity\Github\PullRequestInspection $inspection, Entity\Inspection\Report $report)
    {
        $this->inspection = $inspection;
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
}