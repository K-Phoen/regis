<?php

namespace Tests\Regis\Application\EventListener;

use Regis\Application\Event;
use Regis\Application\EventListener\PullRequestInspectionStatusListener;
use Regis\Application\Github\Client;
use Regis\Domain\Entity\Github\PullRequestInspection;
use Regis\Domain\Entity\Inspection\Report;
use Regis\Domain\Model\Github\PullRequest;

class PullRequestInspectionStatusListenerTest extends \PHPUnit_Framework_TestCase
{
    private $ghClient;
    /** @var PullRequestInspectionStatusListener */
    private $listener;

    public function setUp()
    {
        $this->ghClient = $this->getMockBuilder(Client::class)->getMock();

        $this->listener = new PullRequestInspectionStatusListener($this->ghClient);
    }

    public function testItListensToTheRightEvents()
    {
        $listenedEvents = PullRequestInspectionStatusListener::getSubscribedEvents();

        $this->assertArrayHasKey(Event::PULL_REQUEST_OPENED, $listenedEvents);
        $this->assertArrayHasKey(Event::PULL_REQUEST_SYNCED, $listenedEvents);

        $this->assertArrayHasKey(Event::INSPECTION_STARTED, $listenedEvents);
        $this->assertArrayHasKey(Event::INSPECTION_FAILED, $listenedEvents);
        $this->assertArrayHasKey(Event::INSPECTION_FINISHED, $listenedEvents);
    }

    public function testItSetsTheIntegrationStatusAsScheduledWhenAPrIsOpened()
    {
        $pr = $this->getMockBuilder(PullRequest::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\PullRequestOpened($pr);
        $event = new Event\DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($pr, Client::INTEGRATION_PENDING, $this->stringContains('scheduled'), $this->anything());

        $this->listener->onPullRequestUpdated($event);
    }

    public function testItSetsTheIntegrationStatusAsScheduledWhenAPrIsSynced()
    {
        $pr = $this->getMockBuilder(PullRequest::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\PullRequestSynced($pr, 'before sha', 'after sha');
        $event = new Event\DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($pr, Client::INTEGRATION_PENDING, $this->stringContains('scheduled'), $this->anything());

        $this->listener->onPullRequestUpdated($event);
    }

    public function testItSetsTheIntegrationStatusAsStartedWhenAnInspectionStarts()
    {
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $pr = $this->getMockBuilder(PullRequest::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\InspectionStarted($inspection, $pr);
        $event = new Event\DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($pr, Client::INTEGRATION_PENDING, $this->stringContains('started'), $this->anything());

        $this->listener->onInspectionStarted($event);
    }

    public function testItSetsTheIntegrationStatusAsErroredWhenAnInspectionFails()
    {
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $pr = $this->getMockBuilder(PullRequest::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\InspectionFailed($inspection, $pr, new \Exception());
        $event = new Event\DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($pr, Client::INTEGRATION_ERROR, $this->stringContains('failed'), $this->anything());

        $this->listener->onInspectionFailed($event);
    }

    public function testItSetsTheIntegrationStatusAsFailedWhenAnInspectionFinishesWithErrors()
    {
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $pr = $this->getMockBuilder(PullRequest::class)->disableOriginalConstructor()->getMock();
        $report = $this->getMockBuilder(Report::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\InspectionFinished($inspection, $pr, $report);
        $event = new Event\DomainEventWrapper($domainEvent);

        $report->expects($this->once())
            ->method('hasErrors')
            ->will($this->returnValue(true));

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($pr, Client::INTEGRATION_FAILURE, $this->stringContains('error(s)'), $this->anything());

        $this->listener->onInspectionFinished($event);
    }

    public function testItSetsTheIntegrationStatusAsFailedWhenAnInspectionFinishesWithWarnings()
    {
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $pr = $this->getMockBuilder(PullRequest::class)->disableOriginalConstructor()->getMock();
        $report = $this->getMockBuilder(Report::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\InspectionFinished($inspection, $pr, $report);
        $event = new Event\DomainEventWrapper($domainEvent);

        $report->expects($this->once())
            ->method('hasWarnings')
            ->will($this->returnValue(true));

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($pr, Client::INTEGRATION_FAILURE, $this->stringContains('error(s)'), $this->anything());

        $this->listener->onInspectionFinished($event);
    }

    public function testItSetsTheIntegrationStatusAsSuccessWhenAnInspectionFinishesSuccessfully()
    {
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $pr = $this->getMockBuilder(PullRequest::class)->disableOriginalConstructor()->getMock();
        $report = $this->getMockBuilder(Report::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\InspectionFinished($inspection, $pr, $report);
        $event = new Event\DomainEventWrapper($domainEvent);

        $report->expects($this->once())
            ->method('hasErrors')
            ->will($this->returnValue(false));
        $report->expects($this->once())
            ->method('hasWarnings')
            ->will($this->returnValue(false));

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($pr, Client::INTEGRATION_SUCCESS, $this->stringContains('successful'), $this->anything());

        $this->listener->onInspectionFinished($event);
    }
}
