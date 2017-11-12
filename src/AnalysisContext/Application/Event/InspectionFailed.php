<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application\Event;

use Regis\AnalysisContext\Domain\Entity\Inspection;

class InspectionFailed
{
    private $inspection;
    private $error;

    public function __construct(Inspection $inspection, \Exception $error)
    {
        $this->inspection = $inspection;
        $this->error = $error;
    }

    public function getInspection(): Inspection
    {
        return $this->inspection;
    }

    public function getError(): \Exception
    {
        return $this->error;
    }
}
