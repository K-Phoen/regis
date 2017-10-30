<?php

namespace Tests\Regis\Application\CommandHandler\Github\Inspection;

use PHPUnit\Framework\TestCase;
use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Domain\Entity\Github\PullRequestInspection;
use Regis\Domain\Entity\Inspection\Report;
use Regis\Domain\Repository;

class SavePullRequestReportTest extends TestCase
{
    private $inspectionsRepository;
    /** @var CommandHandler\Github\Inspection\SavePullRequestReport */
    private $handler;

    public function setUp()
    {
        $this->inspectionsRepository = $this->getMockBuilder(Repository\Inspections::class)->disableOriginalConstructor()->getMock();
        $this->handler = new CommandHandler\Github\Inspection\SavePullRequestReport($this->inspectionsRepository);
    }

    public function testItLinksTheReportAndTheInspectionAndSavesBoth()
    {
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $report = $this->getMockBuilder(Report::class)->disableOriginalConstructor()->getMock();

        $inspection->expects($this->once())
            ->method('setReport')
            ->with($report);

        $this->inspectionsRepository->expects($this->once())
            ->method('save')
            ->with($inspection);

        $command = new Command\Github\Inspection\SavePullRequestReport($inspection, $report);

        $this->handler->handle($command);
    }
}
