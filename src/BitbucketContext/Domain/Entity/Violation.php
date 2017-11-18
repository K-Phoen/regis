<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Entity;

class Violation
{
    const WARNING = 10;
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

    public function line(): int
    {
        return $this->line;
    }
}
