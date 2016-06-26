<?php

namespace Tests\Regis\Application\CommandHandler\Github\Inspection;

use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Application\Entity\Github\PullRequestInspection;
use Regis\Application\Entity\Inspection\Report;
use Regis\Application\Entity\Inspection\Violation;
use Regis\Application\Model\Github\PullRequest;
use Regis\Application\Reporter;

class SendViolationsAsCommentsTest extends \PHPUnit_Framework_TestCase
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
        $pullRequest = $this->getMockBuilder(PullRequest::class)->disableOriginalConstructor()->getMock();
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $report = $this->getMockBuilder(Report::class)->disableOriginalConstructor()->getMock();

        $violation1 = $this->getMockBuilder(Violation::class)->disableOriginalConstructor()->getMock();
        $violation2 = $this->getMockBuilder(Violation::class)->disableOriginalConstructor()->getMock();

        $inspection->expects($this->once())
            ->method('getReport')
            ->will($this->returnValue($report));
        $report->expects($this->once())
            ->method('getViolations')
            ->will($this->returnValue(new \ArrayIterator([$violation1, $violation2])));

        $this->reporter->expects($this->exactly(2))
            ->method('report')
            ->withConsecutive(
                [$violation1, $pullRequest],
                [$violation1, $pullRequest]
            );

        $command = new Command\Github\Inspection\SendViolationsAsComments($inspection, $pullRequest);

        $this->handler->handle($command);
    }
}
