<?php

declare(strict_types=1);

namespace Regis\Domain\Entity\Inspection;

use Doctrine\Common\Collections\ArrayCollection;

class Analysis
{
    const STATUS_OK = 'ok';
    const STATUS_WARNING = 'warning';
    const STATUS_ERROR = 'error';

    private $id;
    /** @var ArrayCollection */
    private $violations;
    private $report;
    private $type;
    private $status = self::STATUS_OK;

    public function __construct(string $type)
    {
        $this->violations = new ArrayCollection();
        $this->type = $type;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function addViolation(Violation $violation)
    {
        $violation->setAnalysis($this);
        $this->violations->add($violation);

        if ($violation->isError()) {
            $this->status = self::STATUS_ERROR;
        }

        if ($this->status !== self::STATUS_ERROR && $violation->isWarning()) {
            $this->status = self::STATUS_WARNING;
        }
    }

    public function getViolations(): \Traversable
    {
        return $this->violations;
    }

    public function hasErrors(): bool
    {
        /** @var Violation $violation */
        foreach ($this->violations as $violation) {
            if ($violation->isError()) {
                return true;
            }
        }

        return false;
    }

    public function errorsCount(): int
    {
        return array_reduce($this->violations->toArray(), function(int $count, Violation $violation) {
            return $count + $violation->isError();
        }, 0);
    }

    public function hasWarnings(): bool
    {
        /** @var Violation $violation */
        foreach ($this->violations as $violation) {
            if ($violation->isWarning()) {
                return true;
            }
        }

        return false;
    }

    public function warningsCount(): int
    {
        return array_reduce($this->violations->toArray(), function(int $count, Violation $violation) {
            return $count + $violation->isWarning();
        }, 0);
    }

    public function setReport(Report $report)
    {
        $this->report = $report;
    }

    public function getReport(): Report
    {
        return $this->report;
    }
}