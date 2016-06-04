<?php

declare(strict_types=1);

namespace Regis\Domain\Model\Git;

class Diff
{
    private $base;
    private $head;
    private $files;

    public function __construct(string $base, string $head, array $files)
    {
        $this->base = $base;
        $this->head  = $head;
        $this->files = $files;
    }

    public function getBase(): string
    {
        return $this->base;
    }

    public function getHead(): string
    {
        return $this->head;
    }

    public function getFiles(): array
    {
        return $this->files;
    }
}