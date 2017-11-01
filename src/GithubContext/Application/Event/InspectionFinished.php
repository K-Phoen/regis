<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Event;

use Regis\GithubContext\Application\Event;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Model;

class InspectionFinished implements Event
{
    private $inspection;
    private $pullRequest;
    private $report;

    public function __construct(Entity\Inspection $inspection, Model\PullRequest $pullRequest, Entity\Inspection\Report $report)
    {
        $this->inspection = $inspection;
        $this->pullRequest = $pullRequest;
        $this->report = $report;
    }

    public function getInspection(): Entity\Inspection
    {
        return $this->inspection;
    }

    public function getPullRequest(): Model\PullRequest
    {
        return $this->pullRequest;
    }

    public function getReport(): Entity\Inspection\Report
    {
        return $this->report;
    }

    public function getEventName(): string
    {
        return Event::INSPECTION_FINISHED;
    }
}
