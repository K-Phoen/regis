<?php

declare(strict_types=1);

namespace Regis\Application\Model;

class Violation
{
    const WARNING = 10;
    const ERROR = 20;

    private $severity;
    private $file;
    private $position;
    private $description;

    public static function newError(string $file, int $position, string $description): Violation
    {
        return new static(self::ERROR, $file, $position, $description);
    }

    public static function newWarning(string $file, int $position, string $description): Violation
    {
        return new static(self::WARNING, $file, $position, $description);
    }

    public function __construct(int $severity, string $file, int $position, string $description)
    {
        $this->severity = $severity;
        $this->file = $file;
        $this->position = $position;
        $this->description = $description;
    }

    public function getSeverity(): int
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function __toString(): string
    {
        return sprintf('%s:%d -- %s', $this->file, $this->position, $this->description);
    }
}