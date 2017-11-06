<?php

namespace Tests\Regis\GithubContext\Application\EventListener;

use PHPUnit\Framework\TestCase;
use League\Tactician\CommandBus;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\Event;
use Regis\GithubContext\Application\EventListener\PullRequestReviewCommentsListener;
use Regis\GithubContext\Domain\Entity\PullRequestInspection;
use Regis\GithubContext\Domain\Entity\Report;
use Regis\GithubContext\Domain\Model\PullRequest;

class PullRequestReviewCommentsListenerTest extends TestCase
{
    public function testItListensToTheRightEvents()
    {
        $listenedEvents = PullRequestReviewCommentsListener::getSubscribedEvents();

        $this->assertArrayHasKey(Event::INSPECTION_FINISHED, $listenedEvents);
    }

    public function testItSendsTheRightCommandToTheBus()
    {
        $bus = $this->getMockBuilder(CommandBus::class)->disableOriginalConstructor()->getMock();
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $pr = $this->getMockBuilder(PullRequest::class)->disableOriginalConstructor()->getMock();
        $report = $this->getMockBuilder(Report::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\InspectionFinished($inspection, $pr, $report);
        $event = new Event\DomainEventWrapper($domainEvent);

        $bus->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(Command\Inspection\SendViolationsAsComments::class));

        $listener = new PullRequestReviewCommentsListener($bus);
        $listener->onInspectionFinished($event);
    }
}
