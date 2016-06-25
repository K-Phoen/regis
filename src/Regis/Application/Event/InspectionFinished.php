<?php

declare(strict_types=1);

namespace Regis\Application\Event;

use Regis\Application\Event;
use Regis\Application\Entity;
use Regis\Application\Model\Github\PullRequest;
use Regis\Application\ReportSummary;

class InspectionFinished implements Event
{
    private $inspection;
    private $pullRequest;
    private $reportSummary;

    public function __construct(Entity\Inspection $inspection, PullRequest $pullRequest, ReportSummary $reportSummary)
    {
        $this->inspection = $inspection;
        $this->pullRequest = $pullRequest;
        $this->reportSummary = $reportSummary;
    }

    public function getInspection(): Entity\Inspection
    {
        return $this->inspection;
    }

    public function getPullRequest(): PullRequest
    {
        return $this->pullRequest;
    }

    public function getReportSummary(): ReportSummary
    {
        return $this->reportSummary;
    }

    public function getEventName(): string
    {
        return Event::INSPECTION_FINISHED;
    }
}