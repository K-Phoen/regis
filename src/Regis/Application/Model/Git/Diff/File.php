<?php

declare(strict_types=1);

namespace Regis\Application\Model\Git\Diff;

use Regis\Application\Model\Git\Blob;

class File
{
    private $oldName;
    private $newName;
    private $isBinary;
    private $newBlob;
    /** @var Change[]  */
    private $changes;

    public function __construct(string $oldName, string $newName, bool $isBinary, Blob $newBlob, array $changes)
    {
        $this->oldName = $oldName;
        $this->newName = $newName;
        $this->isBinary = $isBinary;
        $this->newBlob  = $newBlob;
        $this->changes = $changes;
    }

    public function isRename(): bool
    {
        return $this->isModification() && $this->oldName !== $this->newName;
    }

    public function isDeletion(): bool
    {
        return null === $this->newName;
    }

    public function isModification(): bool
    {
        return null !== $this->oldName && null !== $this->newName;
    }

    public function getOldName()
    {
        return $this->oldName;
    }

    public function getNewName()
    {
        return $this->newName;
    }

    public function isBinary()
    {
        return $this->isBinary;
    }

    public function getNewBlob(): Blob
    {
        return $this->newBlob;
    }

    /**
     * @return Change[]
     */
    public function getChanges(): array
    {
        return $this->changes;
    }
}