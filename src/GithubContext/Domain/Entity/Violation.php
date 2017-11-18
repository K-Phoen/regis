<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Entity;

class Violation
{
    private $id;
    private $file;
    private $position;
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

    public function position(): int
    {
        return $this->position;
    }

    public function __toString(): string
    {
        return sprintf('%s:%d -- %s', $this->file, $this->position, $this->description);
    }
}
