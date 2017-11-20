<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Regis\AnalysisContext\Domain\Model\Git\Diff;

use Regis\AnalysisContext\Domain\Model\Exception\LineNotInDiff;
use Regis\AnalysisContext\Domain\Model\Git\Blob;

class File
{
    private $oldName;
    private $newName;
    private $oldIndex;
    private $newIndex;
    private $isBinary;
    private $newBlob;
    /** @var Change[] */
    private $changes;

    public function __construct($oldName, $newName, $oldIndex, $newIndex, bool $isBinary, Blob $newBlob, array $changes)
    {
        $this->oldName = $oldName;
        $this->newName = $newName;
        $this->oldIndex = $oldIndex;
        $this->newIndex = $newIndex;
        $this->isBinary = $isBinary;
        $this->newBlob = $newBlob;
        $this->changes = $changes;
    }

    public function replaceNewContent(Blob $blob): self
    {
        $clone = clone $this;
        $clone->newBlob = $blob;

        return $clone;
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

    public function getNewIndex()
    {
        return $this->newIndex;
    }

    public function isBinary(): bool
    {
        return $this->isBinary;
    }

    public function getNewBlob(): Blob
    {
        return $this->newBlob;
    }

    public function getNewContent(): string
    {
        return $this->newBlob->getContent();
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
        $offset = 0;

        /** @var Change $change */
        foreach ($this->getChanges() as $change) {
            $currentLine = $change->getRangeNewStart();

            /** @var Line $diffLine */
            foreach ($change->getLines() as $diffLine) {
                if ($diffLine->getChangeType() === Change::LINE_REMOVE) {
                    continue;
                }

                if ($currentLine === $line) {
                    return $offset + $diffLine->getPosition();
                }

                ++$currentLine;
            }

            $offset = $diffLine->getPosition() + 1; // We add 1 to skip the line starting by @@
        }

        throw LineNotInDiff::line($line);
    }

    public function isPhp(): bool
    {
        $extension = pathinfo($this->getNewName(), PATHINFO_EXTENSION);

        return \in_array(strtolower($extension), ['php', 'phps'], true);
    }
}
