<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Domain\Entity;

class Analysis
{
    const STATUS_OK = 'ok';
    const STATUS_WARNING = 'warning';
    const STATUS_ERROR = 'error';

    private $id;
    private $report;
    private $warningsCount = 0;
    private $errorsCount = 0;
    private $type;

    /** @var Violation[] */
    private $violations = [];

    public function __construct(Report $report, string $type)
    {
        $this->report = $report;
        $this->type = $type;
    }

    public function addViolation(Violation $violation)
    {
        $violation->setAnalysis($this);
        $this->violations[] = $violation;

        if ($violation->isError()) {
            $this->errorsCount += 1;
        }

        if ($violation->isWarning()) {
            $this->warningsCount += 1;
        }
    }

    public function id(): string
    {
        return $this->id;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function warningsCount(): int
    {
        return $this->warningsCount;
    }

    public function errorsCount(): int
    {
        return $this->errorsCount;
    }

    /**
     * @return Violation[]
     */
    public function violations(): array
    {
        return $this->violations;
    }

    public function hasErrors(): bool
    {
        return $this->errorsCount !== 0;
    }

    public function hasWarnings(): bool
    {
        return $this->warningsCount !== 0;
    }
}
