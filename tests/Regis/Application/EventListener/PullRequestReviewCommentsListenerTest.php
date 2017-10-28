<?php

namespace Tests\Regis\Application\EventListener;

use League\Tactician\CommandBus;
use Regis\Application\Command;
use Regis\Application\Event;
use Regis\Application\EventListener\PullRequestReviewCommentsListener;
use Regis\Domain\Entity\Github\PullRequestInspection;
use Regis\Domain\Entity\Inspection;
use Regis\Domain\Model\Github\PullRequest;

class PullRequestReviewCommentsListenerTest extends \PHPUnit_Framework_TestCase
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
        $report = $this->getMockBuilder(Inspection\Report::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\InspectionFinished($inspection, $pr, $report);
        $event = new Event\DomainEventWrapper($domainEvent);

        $bus->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($command) {
                return $command instanceof Command\Github\Inspection\SendViolationsAsComments;
            }));

        $listener = new PullRequestReviewCommentsListener($bus);
        $listener->onInspectionFinished($event);
    }
}
