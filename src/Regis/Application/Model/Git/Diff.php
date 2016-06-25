<?php

declare(strict_types=1);

namespace Regis\Application\Model\Git;

class Diff
{
    private $revisions;
    private $files;

    public function __construct(Revisions $revisions, array $files)
    {
        $this->revisions = $revisions;
        $this->files = $files;
    }

    public function getBase(): string
    {
        return $this->revisions->getBase();
    }

    public function getHead(): string
    {
        return $this->revisions->getHead();
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getAddedTextFiles(): \Traversable
    {
        foreach ($this->files as $file) {
            if ($file->isBinary() || $file->isRename() || $file->isDeletion()) {
                continue;
            }

            yield $file;
        }
    }
}