<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Analysis
{
    const STATUS_OK = 'ok';
    const STATUS_WARNING = 'warning';
    const STATUS_ERROR = 'error';

    private $id;
    private $type;
    private $report;

    /** @var Violation[] */
    private $violations = [];

    private $violationsMap;
    private $errorsCount;
    private $warningsCount;

    public function __construct(array $violations = [])
    {
        $this->violations = new ArrayCollection($violations);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function status(): string
    {
        if ($this->hasErrors()) {
            return self::STATUS_ERROR;
        }

        if ($this->hasWarnings()) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_OK;
    }

    /**
     * @return Violation[]
     */
    public function violations(): array
    {
        return $this->violations->toArray();
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
        foreach ($this->violations as $violation) {
            $key = sprintf('%s:%d', $violation->file(), $violation->line());

            if (!isset($this->violationsMap[$key])) {
                $this->violationsMap[$key] = [];
            }

            $this->violationsMap[$key][] = $violation;
        }
    }

    public function hasErrors(): bool
    {
        return $this->errorsCount() !== 0;
    }

    public function errorsCount(): int
    {
        if ($this->errorsCount !== null) {
            return $this->errorsCount;
        }

        return $this->errorsCount = array_reduce($this->violations(), function (int $count, Violation $violation) {
            return $count + $violation->isError();
        }, 0);
    }

    public function hasWarnings(): bool
    {
        return $this->warningsCount() !== 0;
    }

    public function warningsCount(): int
    {
        if ($this->warningsCount !== null) {
            return $this->warningsCount;
        }

        return $this->warningsCount = array_reduce($this->violations(), function (int $count, Violation $violation) {
            return $count + $violation->isWarning();
        }, 0);
    }
}
