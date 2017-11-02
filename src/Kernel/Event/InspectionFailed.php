<?php

declare(strict_types=1);

namespace Regis\Kernel\Event;

use Regis\Kernel\Events;

class InspectionFailed implements Events
{
    private $inspectionId;
    private $error;

    public function __construct(string $inspectionId, \Exception $error)
    {
        $this->inspectionId = $inspectionId;
        $this->error = $error;
    }

    public function getInspectionId(): string
    {
        return $this->inspectionId;
    }

    public function getError(): \Exception
    {
        return $this->error;
    }

    public function getEventName(): string
    {
        return Events::INSPECTION_FAILED;
    }
}
