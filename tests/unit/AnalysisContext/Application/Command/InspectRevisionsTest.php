<?php

namespace Tests\Regis\AnalysisContext\Application\CommandHandler;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Application\Inspector;
use Regis\AnalysisContext\Application\Command;
use Regis\AnalysisContext\Application\CommandHandler;
use Regis\AnalysisContext\Domain\Model;

class InspectRevisionsTest extends TestCase
{
    /** @var Inspector */
    private $inspector;

    /** @var CommandHandler\InspectRevisions */
    private $handler;

    public function setUp()
    {
        $this->inspector = $this->createMock(Inspector::class);

        $this->handler = new CommandHandler\InspectRevisions($this->inspector);
    }

    public function testTheInspectionIsDelegatedToTheInspector()
    {
        $report = new Model\Inspection\Report('raw diff');
        $repository = new Model\Git\Repository('owner', 'name', 'clone url');
        $revisions = new Model\Git\Revisions('base', 'head');

        $this->inspector->expects($this->once())
            ->method('inspect')
            ->with($repository, $revisions)
            ->willReturn($report);

        $this->assertSame($report, $this->handler->handle(new Command\InspectRevisions($repository, $revisions)));
    }
}
