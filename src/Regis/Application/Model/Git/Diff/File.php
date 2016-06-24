<?php

declare(strict_types=1);

namespace Regis\Application\Model\Git\Diff;

use Regis\Application\Model\Exception\LineNotInDiff;
use Regis\Application\Model\Git\Blob;

class File
{
    private $oldName;
    private $newName;
    private $isBinary;
    private $newBlob;
    /** @var Change[]  */
    private $changes;

    public function __construct($oldName, $newName, bool $isBinary, Blob $newBlob, array $changes)
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

    public function isCreation(): bool
    {
        return null === $this->oldName;
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

    public function findPositionForLine(int $line): int
    {
        $changes = $this->getChanges();
        $offset = 0;

        /** @var Change $change */
        foreach ($changes as $change) {
            $currentLine = $change->getRangeNewStart();

            /** @var Line $diffLine */
            foreach ($change->getLines() as $diffLine) {
                if ($diffLine->getChangeType() === Change::LINE_REMOVE) {
                    continue;
                }

                if ($currentLine === $line) {
                    return $offset + $diffLine->getPosition();
                }

                $currentLine += 1;
            }

            $offset = $diffLine->getPosition() + 1; // We add 1 to skip the line starting by @@
        }

        throw LineNotInDiff::line($line);
    }
}