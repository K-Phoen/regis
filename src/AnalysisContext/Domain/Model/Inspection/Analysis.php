<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Domain\Model\Inspection;

class Analysis
{
    const STATUS_OK = 'ok';
    const STATUS_WARNING = 'warning';
    const STATUS_ERROR = 'error';

    /** @var Violation[] */
    private $violations = [];
    private $type;
    private $status = self::STATUS_OK;

    private $violationsMap;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function violationsAtLine(string $file, int $line): array
    {
        if ($this->violationsMap === null) {
            $this->buildViolationsMap();
        }

        if (!isset($this->violationsMap[sprintf('%s:%d', $file, $line)])) {
            return [];
        }

        return $this->violationsMap[sprintf('%s:%d', $file, $line)];
    }

    private function buildViolationsMap()
    {
        $this->violationsMap = [];

        /** @var Violation $violation */
        foreach ($this->violations() as $violation) {
            $key = sprintf('%s:%d', $violation->file(), $violation->line());

            if (!isset($this->violationsMap[$key])) {
                $this->violationsMap[$key] = [];
            }

            $this->violationsMap[$key][] = $violation;
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

    public function addViolation(Violation $violation)
    {
        $this->violationsMap = null;

        $violation->setAnalysis($this);
        $this->violations[] = $violation;

        if ($violation->isError()) {
            $this->status = self::STATUS_ERROR;
        }

        if ($this->status !== self::STATUS_ERROR && $violation->isWarning()) {
            $this->status = self::STATUS_WARNING;
        }
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
        return array_reduce($this->violations, function (int $count, Violation $violation) {
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
        return array_reduce($this->violations, function (int $count, Violation $violation) {
            return $count + $violation->isWarning();
        }, 0);
    }
}
