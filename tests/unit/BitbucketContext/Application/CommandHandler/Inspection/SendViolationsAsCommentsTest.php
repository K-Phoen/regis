<?php

declare(strict_types=1);

namespace Tests\Regis\BitbucketContext\Application\CommandHandler\Inspection;

use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Application\CommandHandler;
use Regis\BitbucketContext\Application\Reporter;
use Regis\BitbucketContext\Domain\Entity\PullRequestInspection;
use Regis\BitbucketContext\Domain\Entity\Report;
use Regis\BitbucketContext\Domain\Entity\Violation;
use Regis\BitbucketContext\Domain\Entity\Repository;
use Regis\BitbucketContext\Domain\Model\PullRequest;
use Regis\BitbucketContext\Domain\Model\ReviewComment;

class SendViolationsAsCommentsTest extends TestCase
{
    private $reporter;
    /** @var CommandHandler\Inspection\SendViolationsAsComments */
    private $handler;

    public function setUp()
    {
        $this->reporter = $this->createMock(Reporter::class);

        $this->handler = new CommandHandler\Inspection\SendViolationsAsComments($this->reporter);
    }

    public function testViolationsAreTransferedToTheReporter()
    {
        $repository = $this->createMock(Repository::class);
        $pullRequest = $this->createMock(PullRequest::class);
        $inspection = $this->createMock(PullRequestInspection::class);
        $report = $this->createMock(Report::class);

        $violation1 = $this->createMock(Violation::class);
        $violation2 = $this->createMock(Violation::class);

        $inspection->method('getReport')->willReturn($report);
        $inspection->method('hasReport')->willReturn(true);
        $inspection->method('getRepository')->willReturn($repository);
        $inspection->method('getPullRequest')->willReturn($pullRequest);
        $report->method('violations')->willReturn(new \ArrayIterator([$violation1, $violation2]));

        $this->reporter->expects($this->exactly(2))
            ->method('report')
            ->withConsecutive(
                [$repository, $this->isInstanceOf(ReviewComment::class), $pullRequest],
                [$repository, $this->isInstanceOf(ReviewComment::class), $pullRequest]
            );

        $command = new Command\Inspection\SendViolationsAsComments($inspection);

        $this->handler->handle($command);
    }

    public function testAnInspectionWithNoReportTriggersNothing()
    {
        $inspection = $this->createMock(PullRequestInspection::class);

        $inspection->method('hasReport')->willReturn(false);

        $inspection->expects($this->never())->method('getReport');
        $this->reporter->expects($this->never())->method('report');

        $command = new Command\Inspection\SendViolationsAsComments($inspection);

        $this->handler->handle($command);
    }
}
