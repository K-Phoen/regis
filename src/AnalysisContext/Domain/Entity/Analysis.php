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
    private $status = self::STATUS_OK;

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
            $this->status = self::STATUS_ERROR;
            $this->errorsCount += 1;
        }

        if ($violation->isWarning()) {
            $this->warningsCount += 1;
        }

        if ($this->status !== self::STATUS_ERROR && $violation->isWarning()) {
            $this->status = self::STATUS_WARNING;
        }
    }

    public function type(): string
    {
        return $this->type;
    }

    public function status(): string
    {
        return $this->status;
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
