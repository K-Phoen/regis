<?php

declare(strict_types=1);

namespace Regis\Kernel\Event;

use Regis\Kernel\Events;

class InspectionFinished implements Events
{
    private $inspectionId;

    public function __construct(string $inspectionId)
    {
        $this->inspectionId = $inspectionId;
    }

    public function getInspectionId(): string
    {
        return $this->inspectionId;
    }

    public function getEventName(): string
    {
        return Events::INSPECTION_FINISHED;
    }
}
