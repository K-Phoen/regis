<?php

declare(strict_types=1);

namespace Regis\Domain\Model;

class Violation
{
    private $file;
    private $position;
    private $description;

    public function __construct(string $file, int $position, string $description)
    {
        $this->file = $file;
        $this->position = $position;
        $this->description = $description;
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