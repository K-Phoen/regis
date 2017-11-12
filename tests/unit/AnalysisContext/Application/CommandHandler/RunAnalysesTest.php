<?php

namespace Tests\Regis\AnalysisContext\Application\CommandHandler;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Application\Inspector;
use Regis\AnalysisContext\Application\Command;
use Regis\AnalysisContext\Application\CommandHandler;
use Regis\AnalysisContext\Domain\Entity\Report;
use Regis\AnalysisContext\Domain\Model;

class RunAnalysesTest extends TestCase
{
    /** @var Inspector */
    private $inspector;

    /** @var CommandHandler\RunAnalyses */
    private $handler;

    public function setUp()
    {
        $this->inspector = $this->createMock(Inspector::class);

        $this->handler = new CommandHandler\RunAnalyses($this->inspector);
    }

    public function testTheInspectionIsDelegatedToTheInspector()
    {
        $report = $this->createMock(Report::class);
        $repository = $this->createMock(Model\Git\Repository::class);
        $revisions = $this->createMock(Model\Git\Revisions::class);

        $this->inspector->expects($this->once())
            ->method('inspect')
            ->with($repository, $revisions)
            ->willReturn($report);

        $this->assertSame($report, $this->handler->handle(new Command\RunAnalyses($repository, $revisions)));
    }
}
