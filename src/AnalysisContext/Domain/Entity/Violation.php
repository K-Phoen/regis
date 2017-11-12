<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Domain\Entity;

class Violation
{
    const WARNING = 10;
    const ERROR = 20;

    private $id;
    private $severity;
    private $file;
    private $position;
    private $line;
    private $description;

    /** @var Analysis */
    private $analysis;

    public static function newError(string $file, int $line, int $position, string $description): self
    {
        return new static(self::ERROR, $file, $line, $position, $description);
    }

    public static function newWarning(string $file, int $line, int $position, string $description): self
    {
        return new static(self::WARNING, $file, $line, $position, $description);
    }

    public function __construct(int $severity, string $file, int $line, int $position, string $description)
    {
        $this->severity = $severity;
        $this->file = $file;
        $this->line = $line;
        $this->position = $position;
        $this->description = $description;
    }

    public function analysis(): Analysis
    {
        return $this->analysis;
    }

    public function setAnalysis(Analysis $analysis)
    {
        $this->analysis = $analysis;
    }

    public function severity(): int
    {
        return $this->severity;
    }

    public function isError(): bool
    {
        return $this->severity === self::ERROR;
    }

    public function isWarning(): bool
    {
        return $this->severity === self::WARNING;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function file(): string
    {
        return $this->file;
    }

    public function position(): int
    {
        return $this->position;
    }

    public function line(): int
    {
        return $this->line;
    }
}
