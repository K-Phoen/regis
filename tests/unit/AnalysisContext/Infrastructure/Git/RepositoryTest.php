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

namespace Tests\Regis\AnalysisContext\Infrastructure\Git;

use PHPUnit\Framework\TestCase;
use Gitonomy\Git as Gitonomy;
use Regis\AnalysisContext\Application\Vcs\FileNotFound;
use Regis\AnalysisContext\Domain\Model\Git as Model;
use Regis\AnalysisContext\Infrastructure\Git\Repository;
use Symfony\Component\Filesystem\Filesystem;

class RepositoryTest extends TestCase
{
    public function testCheckout()
    {
        $gitonomyRepository = $this->createMock(Gitonomy\Repository::class);
        $gitonomyWorkingCopy = $this->createMock(Gitonomy\WorkingCopy::class);
        $repository = new Repository($gitonomyRepository, new Filesystem());

        $gitonomyRepository->method('getWorkingCopy')->willReturn($gitonomyWorkingCopy);

        $gitonomyRepository
            ->expects($this->once())
            ->method('run')
            ->with('fetch');

        $gitonomyWorkingCopy
            ->expects($this->once())
            ->method('checkout')
            ->with('revision hash');

        $repository->checkout('revision hash');
    }

    public function testRoot()
    {
        $gitonomyRepository = $this->createMock(Gitonomy\Repository::class);
        $repository = new Repository($gitonomyRepository, new Filesystem());

        $gitonomyRepository->method('getPath')->willReturn('/repo/root/dir');

        $this->assertSame('/repo/root/dir', $repository->root());
    }

    public function testLocateFileRaisesAnErrorWhenTheFileDoesNotExist()
    {
        $fs = $this->createMock(Filesystem::class);
        $gitonomyRepository = $this->createMock(Gitonomy\Repository::class);
        $repository = new Repository($gitonomyRepository, $fs);

        $gitonomyRepository->method('getPath')->willReturn('/repo/root/dir');
        $fs->method('exists')->with('/repo/root/dir/filename')->willReturn(false);

        $this->expectException(FileNotFound::class);

        $repository->locateFile('filename');
    }

    public function testLocateFileReturnsAnAbsolutePathWhenTheFileExists()
    {
        $fs = $this->createMock(Filesystem::class);
        $gitonomyRepository = $this->createMock(Gitonomy\Repository::class);
        $repository = new Repository($gitonomyRepository, $fs);

        $gitonomyRepository->method('getPath')->willReturn('/repo/root/dir');
        $fs->method('exists')->with('/repo/root/dir/filename')->willReturn(true);

        $this->assertSame('/repo/root/dir/filename', $repository->locateFile('filename'));
    }

    public function testGetDiff()
    {
        $gitonomyRepository = new Gitonomy\Repository(APP_ROOT_DIR);
        $repository = new Repository($gitonomyRepository, new Filesystem());
        $revisions = new Model\Revisions('5445fc28ee3b6c01194f7df770bb79783a16af45', '17ed7410514072f74352fd0a91586d7684eef886');

        $diff = $repository->getDiff($revisions);

        $this->assertInstanceOf(Model\Diff::class, $diff);
        $this->assertCount(4, $diff->getFiles());

        $this->assertSame([
            'src/Regis/Application/Inspection/PhpMd.php',
            'tests/Regis/Application/Inspection/CodeSnifferTest.php',
            'tests/Regis/Application/Inspection/InspectionTestCase.php',
            'tests/Regis/Application/Inspection/PhpMdTest.php',
        ], array_map(function (Model\Diff\File $file) {
            return $file->getNewName();
        }, $diff->getFiles()));
    }
}
