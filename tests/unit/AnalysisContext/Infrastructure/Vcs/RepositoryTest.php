<?php

namespace Tests\Regis\AnalysisContext\Infrastructure\Vcs;

use PHPUnit\Framework\TestCase;
use Gitonomy\Git as Gitonomy;

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

    public function testGetDiff()
    {
        $gitonomyRepository = new Gitonomy\Repository(APP_ROOT_DIR);
        $repository = new Repository($gitonomyRepository, new Filesystem());
        $revisions = new Model\Revisions('5445fc28ee3b6c01194f7df770bb79783a16af45', '17ed7410514072f74352fd0a91586d7684eef886');

        $diff = $repository->getDiff($revisions);

        $this->assertInstanceOf(Model\Diff::class, $diff);
        $this->assertCount(4, $diff->getFiles());

        $this->assertEquals([
            'src/Regis/Application/Inspection/PhpMd.php',
            'tests/Regis/Application/Inspection/CodeSnifferTest.php',
            'tests/Regis/Application/Inspection/InspectionTestCase.php',
            'tests/Regis/Application/Inspection/PhpMdTest.php',
        ], array_map(function (Model\Diff\File $file) {
            return $file->getNewName();
        }, $diff->getFiles()));

        $this->assertNotEquals('dummy content', $diff->getFiles()[0]->getNewContent());
    }
}
