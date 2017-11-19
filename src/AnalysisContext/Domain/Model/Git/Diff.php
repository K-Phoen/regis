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

namespace Regis\AnalysisContext\Domain\Model\Git;

use Regis\AnalysisContext\Domain\Git\DiffParser;

class Diff
{
    private $revisions;
    private $files;
    private $rawDiff;

    public static function fromRawDiff(Revisions $revisions, string $rawDiff): self
    {
        $files = (new DiffParser())->parse($rawDiff);

        return new static($revisions, $files, $rawDiff);
    }

    public function __construct(Revisions $revisions, array $files, string $rawDiff)
    {
        $this->revisions = $revisions;
        $this->files = $files;
        $this->rawDiff = $rawDiff;
    }

    public function getRevisions(): Revisions
    {
        return $this->revisions;
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

    public function getRawDiff(): string
    {
        return $this->rawDiff;
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

    public function getAddedPhpFiles(): \Traversable
    {
        /** @var Diff\File $file */
        foreach ($this->getAddedTextFiles() as $file) {
            if ($file->isPhp()) {
                yield $file;
            }
        }
    }
}
