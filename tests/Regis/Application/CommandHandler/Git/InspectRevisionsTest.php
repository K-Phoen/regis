<?php

namespace Tests\Regis\Application\CommandHandler\Git;

use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Application\Inspector;
use Regis\Domain\Entity;
use Regis\Domain\Model;

class InspectRevisionsTest extends \PHPUnit_Framework_TestCase
{
    /** @var Inspector */
    private $inspector;

    /** @var CommandHandler\Git\InspectRevisions */
    private $handler;

    public function setUp()
    {
        $this->inspector = $this->getMockBuilder(Inspector::class)->disableOriginalConstructor()->getMock();

        $this->handler = new CommandHandler\Git\InspectRevisions($this->inspector);
    }

    public function testTheInspectionIsDelegatedToTheInspector()
    {
        $report = new Entity\Inspection\Report('raw diff');
        $repository = new Model\Git\Repository('owner', 'name', 'clone url');
        $revisions = new Model\Git\Revisions('base', 'head');

        $this->inspector->expects($this->once())
            ->method('inspect')
            ->with($repository, $revisions)
            ->will($this->returnValue($report));

        $this->assertSame($report, $this->handler->handle(new Command\Git\InspectRevisions($repository, $revisions)));
    }
}
