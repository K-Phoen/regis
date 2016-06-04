<?php

declare(strict_types=1);

namespace Regis\Domain\Model;

class Violation
{
    private $file;
    private $position;
    private $description;
    private $commit;

    public function __construct(string $file, int $position, string $description, Commit $commit = null)
    {
        $this->file = $file;
        $this->position = $position;
        $this->description = $description;
        $this->commit = $commit;
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

    /**
     * @return Commit|null
     */
    public function getCommit()
    {
        return $this->commit;
    }

}