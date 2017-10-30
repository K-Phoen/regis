<?php

namespace Tests\Regis\Application\CommandHandler\Github\Inspection;

use PHPUnit\Framework\TestCase;
use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Application\Reporter;
use Regis\Domain\Entity\Github\PullRequestInspection;
use Regis\Domain\Entity\Inspection\Report;
use Regis\Domain\Entity\Inspection\Violation;
use Regis\Domain\Entity\Repository;
use Regis\Domain\Model\Github\PullRequest;

class SendViolationsAsCommentsTest extends TestCase
{
    private $reporter;
    /** @var CommandHandler\Github\Inspection\SendViolationsAsComments */
    private $handler;

    public function setUp()
    {
        $this->reporter = $this->getMockBuilder(Reporter::class)->getMock();
        $this->handler = new CommandHandler\Github\Inspection\SendViolationsAsComments($this->reporter);
    }

    public function testViolationsAreTransferedToTheReporter()
    {
        $repository = $this->getMockBuilder(Repository::class)->disableOriginalConstructor()->getMock();
        $pullRequest = $this->getMockBuilder(PullRequest::class)->disableOriginalConstructor()->getMock();
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $report = $this->getMockBuilder(Report::class)->disableOriginalConstructor()->getMock();

        $violation1 = $this->getMockBuilder(Violation::class)->disableOriginalConstructor()->getMock();
        $violation2 = $this->getMockBuilder(Violation::class)->disableOriginalConstructor()->getMock();

        $inspection->expects($this->once())
            ->method('getReport')
            ->will($this->returnValue($report));
        $inspection->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repository));
        $report->expects($this->once())
            ->method('getViolations')
            ->will($this->returnValue(new \ArrayIterator([$violation1, $violation2])));

        $this->reporter->expects($this->exactly(2))
            ->method('report')
            ->withConsecutive(
                [$repository, $violation1, $pullRequest],
                [$repository, $violation1, $pullRequest]
            );

        $command = new Command\Github\Inspection\SendViolationsAsComments($inspection, $pullRequest);

        $this->handler->handle($command);
    }
}
