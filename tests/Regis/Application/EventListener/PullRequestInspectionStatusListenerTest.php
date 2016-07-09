<?php

namespace Tests\Regis\Application\EventListener;

use Regis\Application\Event;
use Regis\Application\EventListener\PullRequestInspectionStatusListener;
use Regis\Application\Github\Client;
use Regis\Application\Github\ClientFactory;
use Regis\Domain\Entity;
use Regis\Domain\Entity\Github\PullRequestInspection;
use Regis\Domain\Entity\Inspection\Report;
use Regis\Domain\Model\Github\PullRequest;
use Regis\Domain\Repository;

class PullRequestInspectionStatusListenerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ClientFactory */
    private $ghClientFactory;
    /** @var Client */
    private $ghClient;
    /** @var Repository\Repositories */
    private $repoRepository;
    /** @var Entity\Github\Repository */
    private $repository;
    /** @var PullRequest */
    private $pr;
    /** @var PullRequestInspectionStatusListener */
    private $listener;

    public function setUp()
    {
        $this->ghClientFactory = $this->getMockBuilder(ClientFactory::class)->getMock();
        $this->ghClient = $this->getMockBuilder(Client::class)->getMock();
        $this->repoRepository = $this->getMockBuilder(Repository\Repositories::class)->getMock();
        $this->pr = $this->getMockBuilder(PullRequest::class)->disableOriginalConstructor()->getMock();
        $this->repository = $this->getMockBuilder(Entity\Github\Repository::class)->disableOriginalConstructor()->getMock();

        $this->pr->expects($this->any())
            ->method('getRepositoryIdentifier')
            ->will($this->returnValue('repository/identifier'));

        $this->repoRepository->expects($this->any())
            ->method('find')
            ->with('repository/identifier')
            ->will($this->returnValue($this->repository));

        $this->ghClientFactory->expects($this->any())
            ->method('createForRepository')
            ->with($this->repository)
            ->will($this->returnValue($this->ghClient));

        $this->listener = new PullRequestInspectionStatusListener($this->ghClientFactory, $this->repoRepository);
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
        $domainEvent = new Event\PullRequestOpened($this->pr);
        $event = new Event\DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($this->pr, Client::INTEGRATION_PENDING, $this->stringContains('scheduled'), $this->anything());

        $this->listener->onPullRequestUpdated($event);
    }

    public function testItSetsTheIntegrationStatusAsScheduledWhenAPrIsSynced()
    {
        $domainEvent = new Event\PullRequestSynced($this->pr, 'before sha', 'after sha');
        $event = new Event\DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($this->pr, Client::INTEGRATION_PENDING, $this->stringContains('scheduled'), $this->anything());

        $this->listener->onPullRequestUpdated($event);
    }

    public function testItSetsTheIntegrationStatusAsStartedWhenAnInspectionStarts()
    {
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\InspectionStarted($inspection, $this->pr);
        $event = new Event\DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($this->pr, Client::INTEGRATION_PENDING, $this->stringContains('started'), $this->anything());

        $this->listener->onInspectionStarted($event);
    }

    public function testItSetsTheIntegrationStatusAsErroredWhenAnInspectionFails()
    {
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\InspectionFailed($inspection, $this->pr, new \Exception());
        $event = new Event\DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($this->pr, Client::INTEGRATION_ERROR, $this->stringContains('failed'), $this->anything());

        $this->listener->onInspectionFailed($event);
    }

    public function testItSetsTheIntegrationStatusAsFailedWhenAnInspectionFinishesWithErrors()
    {
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $report = $this->getMockBuilder(Report::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\InspectionFinished($inspection, $this->pr, $report);
        $event = new Event\DomainEventWrapper($domainEvent);

        $report->expects($this->once())
            ->method('hasErrors')
            ->will($this->returnValue(true));

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($this->pr, Client::INTEGRATION_FAILURE, $this->stringContains('error(s)'), $this->anything());

        $this->listener->onInspectionFinished($event);
    }

    public function testItSetsTheIntegrationStatusAsFailedWhenAnInspectionFinishesWithWarnings()
    {
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $report = $this->getMockBuilder(Report::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\InspectionFinished($inspection, $this->pr, $report);
        $event = new Event\DomainEventWrapper($domainEvent);

        $report->expects($this->once())
            ->method('hasWarnings')
            ->will($this->returnValue(true));

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($this->pr, Client::INTEGRATION_FAILURE, $this->stringContains('error(s)'), $this->anything());

        $this->listener->onInspectionFinished($event);
    }

    public function testItSetsTheIntegrationStatusAsSuccessWhenAnInspectionFinishesSuccessfully()
    {
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $report = $this->getMockBuilder(Report::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\InspectionFinished($inspection, $this->pr, $report);
        $event = new Event\DomainEventWrapper($domainEvent);

        $report->expects($this->once())
            ->method('hasErrors')
            ->will($this->returnValue(false));
        $report->expects($this->once())
            ->method('hasWarnings')
            ->will($this->returnValue(false));

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($this->pr, Client::INTEGRATION_SUCCESS, $this->stringContains('successful'), $this->anything());

        $this->listener->onInspectionFinished($event);
    }
}
