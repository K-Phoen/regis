<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Entity;

class Violation
{
    private $id;
    private $file;
    private $line;
    private $description;

    /** @var Analysis */
    private $analysis;

    public function analysis(): Analysis
    {
        return $this->analysis;
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
