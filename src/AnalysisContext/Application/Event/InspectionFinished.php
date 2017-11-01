<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application\Event;

use Regis\Event\Events;
use Regis\AnalysisContext\Domain\Model;

class InspectionFinished implements Events
{
    private $inspectionId;
    private $report;

    public function __construct(string $inspectionId, Model\Inspection\Report $report)
    {
        $this->inspectionId = $inspectionId;
        $this->report = $report;
    }

    public function getInspectionId(): string
    {
        return $this->inspectionId;
    }

    public function getReport(): Model\Inspection\Report
    {
        return $this->report;
    }

    public function getEventName(): string
    {
        return Events::INSPECTION_FINISHED;
    }
}
