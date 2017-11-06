<?php

namespace Tests\Regis\AnalysisContext\Application;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Application\Inspection;
use Regis\AnalysisContext\Application\Inspector;
use Regis\AnalysisContext\Domain\Entity\Violation;
use Regis\AnalysisContext\Domain\Model;
use Regis\AnalysisContext\Application\Vcs;

class InspectorTest extends TestCase
{
    const REVISION_HEAD = 'revision head hash';

    private $git;
    private $repository;
    private $gitRepository;
    private $diff;
    private $revisions;

    public function setUp()
    {
        $this->git = $this->createMock(Vcs\Git::class);
        $this->gitRepository = $this->createMock(Vcs\Repository::class);

        $this->diff = $this->createMock(Model\Git\Diff::class);
        $this->repository = $this->createMock(Model\Git\Repository::class);
        $this->revisions = $this->createMock(Model\Git\Revisions::class);

        $this->revisions->method('getHead')->willReturn(self::REVISION_HEAD);
        $this->git->method('getRepository')->with($this->repository)->willReturn($this->gitRepository);
        $this->gitRepository->method('getDiff')->with($this->revisions)->willReturn($this->diff);
    }

    public function testItStartsByCheckoutOutTheGitRepo()
    {
        $inspector = new Inspector($this->git);

        $this->gitRepository->expects($this->once())
            ->method('checkout')
            ->with(self::REVISION_HEAD);

        $inspector->inspect($this->repository, $this->revisions);
    }

    public function testItGivesTheDiffToTheInspectionsAndReturnsAUnifiedReport()
    {
        $inspection1 = $this->createMock(Inspection::class);
        $inspection2 = $this->createMock(Inspection::class);

        $violation1 = $this->createMock(Violation::class);
        $violation2 = $this->createMock(Violation::class);

        $inspection1->expects($this->once())
            ->method('inspectDiff')
            ->with($this->gitRepository, $this->diff)
            ->willReturn(new \ArrayIterator([$violation1]));

        $inspection2->expects($this->once())
            ->method('inspectDiff')
            ->with($this->gitRepository, $this->diff)
            ->willReturn(new \ArrayIterator([$violation2]));

        $inspector = new Inspector($this->git, [$inspection1, $inspection2]);
        $report = $inspector->inspect($this->repository, $this->revisions);

        $this->assertCount(2, iterator_to_array($report->violations()));
    }
}
