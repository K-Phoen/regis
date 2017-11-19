<?php

declare(strict_types=1);

namespace Regis\AppContext\Domain\Entity;

class Violation
{
    const ERROR = 20;

    private $id;
    private $severity;
    private $file;
    private $line;
    private $description;

    /** @var Analysis */
    private $analysis;

    public function analysis(): Analysis
    {
        return $this->analysis;
    }

    public function isError(): bool
    {
        return $this->severity === self::ERROR;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function file(): string
    {
        return $this->file;
    }

    public function line(): int
    {
        return $this->line;
    }
}
