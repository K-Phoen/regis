<?php

namespace Tests\Regis\Application\EventListener;

use League\Tactician\CommandBus;
use Regis\Application\Command;
use Regis\Application\Entity\Github\PullRequestInspection;
use Regis\Application\Entity\Inspection;
use Regis\Application\Event;
use Regis\Application\EventListener\PullRequestInspectionReportListener;
use Regis\Application\Model\Github\PullRequest;
use Regis\Symfony\Event\DomainEventWrapper;

class PullRequestInspectionReportListenerTest extends \PHPUnit_Framework_TestCase
{
    private $bus;
    private $listener;

    public function setUp()
    {
        $this->bus = $this->getMockBuilder(CommandBus::class)->disableOriginalConstructor()->getMock();

        $this->listener = new PullRequestInspectionReportListener($this->bus);
    }

    public function testItListensToTheRightEvents()
    {
        $listenedEvents = PullRequestInspectionReportListener::getSubscribedEvents();

        $this->assertArrayHasKey(Event::INSPECTION_FINISHED, $listenedEvents);
    }

    public function testItSendsTheRightCommandToTheBus()
    {
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $pr = $this->getMockBuilder(PullRequest::class)->disableOriginalConstructor()->getMock();
        $report = $this->getMockBuilder(Inspection\Report::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\InspectionFinished($inspection, $pr, $report);
        $event = new DomainEventWrapper($domainEvent);

        $this->bus->expects($this->once())
            ->method('handle')
            ->with($this->callback(function($command) {
                return $command instanceof Command\Github\Inspection\SavePullRequestReport;
            }));

        $this->listener->onInspectionFinished($event);
    }
}
