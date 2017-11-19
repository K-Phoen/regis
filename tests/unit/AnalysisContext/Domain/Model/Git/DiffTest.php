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

namespace Tests\Regis\AnalysisContext\Domain\Model\Git;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Domain\Model\Git;

class DiffTest extends TestCase
{
    public function testHeadAndBaseAreGetFromTheRevision()
    {
        $revisions = $this->revisions();
        $revisions->expects($this->once())
            ->method('getBase')
            ->willReturn('base sha');
        $revisions->expects($this->once())
            ->method('getHead')
            ->willReturn('head sha');

        $diff = new Git\Diff($revisions, [], 'raw diff');

        $this->assertSame('base sha', $diff->getBase());
        $this->assertSame('head sha', $diff->getHead());
        $this->assertSame('raw diff', $diff->getRawDiff());
    }

    public function testGetAddedPhpFiles()
    {
        $binaryFile = $this->binaryFile();
        $deletedFile = $this->binaryFile();
        $renamedFile = $this->renamedFile();
        $textFile = $this->textFile();
        $phpFile = $this->phpFile();
        $diff = new Git\Diff($this->revisions(), [$binaryFile, $deletedFile, $renamedFile, $textFile, $phpFile], 'raw diff');

        $this->assertSame([$phpFile], iterator_to_array($diff->getAddedPhpFiles()));
    }

    public function testGetAddedTextFilesExcludesBinaryFiles()
    {
        $binaryFile = $this->binaryFile();
        $textFile = $this->textFile();
        $diff = new Git\Diff($this->revisions(), [$binaryFile, $textFile], 'raw diff');

        $this->assertSame([$textFile], iterator_to_array($diff->getAddedTextFiles()));
        $this->assertSame([$binaryFile, $textFile], $diff->getFiles());
    }

    public function testGetAddedTextFilesExcludesRenamedFiles()
    {
        $renamedFile = $this->renamedFile();
        $textFile = $this->textFile();
        $diff = new Git\Diff($this->revisions(), [$renamedFile, $textFile], 'raw diff');

        $this->assertSame([$textFile], iterator_to_array($diff->getAddedTextFiles()));
        $this->assertSame([$renamedFile, $textFile], $diff->getFiles());
    }

    public function testGetAddedTextFilesExcludesDeletedFiles()
    {
        $deletedFile = $this->deletedFile();
        $textFile = $this->textFile();
        $diff = new Git\Diff($this->revisions(), [$deletedFile, $textFile], 'raw diff');

        $this->assertSame([$textFile], iterator_to_array($diff->getAddedTextFiles()));
        $this->assertSame([$deletedFile, $textFile], $diff->getFiles());
    }

    private function revisions(): Git\Revisions
    {
        return $this->createMock(Git\Revisions::class);
    }

    private function binaryFile(): Git\Diff\File
    {
        $file = $this->createMock(Git\Diff\File::class);
        $file->method('isBinary')->willReturn(true);

        return $file;
    }

    private function renamedFile(): Git\Diff\File
    {
        $file = $this->createMock(Git\Diff\File::class);
        $file->method('isRename')->willReturn(true);

        return $file;
    }

    private function deletedFile(): Git\Diff\File
    {
        $file = $this->createMock(Git\Diff\File::class);
        $file->method('isDeletion')->willReturn(true);

        return $file;
    }

    private function textFile(): Git\Diff\File
    {
        return $this->createMock(Git\Diff\File::class);
    }

    private function phpFile(): Git\Diff\File
    {
        $file = $this->createMock(Git\Diff\File::class);
        $file->method('isPhp')->willReturn(true);

        return $file;
    }
}
