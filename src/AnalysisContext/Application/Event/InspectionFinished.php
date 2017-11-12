<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application\Event;

use Regis\AnalysisContext\Domain\Entity\Inspection;

class InspectionFinished
{
    private $inspection;

    public function __construct(Inspection $inspection)
    {
        $this->inspection = $inspection;
    }

    public function getInspection(): Inspection
    {
        return $this->inspection;
    }
}
