<?php

declare(strict_types=1);

namespace Regis\Application\Event;

use Regis\Application\Event;
use Regis\Application\Model\Github\PullRequest;
use Regis\Application\ReportSummary;

class InspectionFinished implements Event
{
    private $pullRequest;
    private $reportSummary;

    public function __construct(PullRequest $pullRequest, ReportSummary $reportSummary)
    {
        $this->pullRequest = $pullRequest;
        $this->reportSummary = $reportSummary;
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