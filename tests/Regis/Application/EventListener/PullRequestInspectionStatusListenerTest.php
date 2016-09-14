<?php

namespace Tests\Regis\Application\EventListener;

use Regis\Application\Github\IntegrationStatus;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as UrlGenerator;
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
    /** @var UrlGenerator */
    private $urlGenerator;
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
        $this->urlGenerator = $this->getMockBuilder(UrlGenerator::class)->getMock();

        $this->repository->expects($this->any())
            ->method('isInspectionEnabled')
            ->will($this->returnValue(true));

        $this->urlGenerator->expects($this->any())
            ->method('generate')
            ->will($this->returnValue('something not null'));

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

        $this->listener = new PullRequestInspectionStatusListener($this->ghClientFactory, $this->repoRepository, $this->urlGenerator);
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
            ->with($this->pr, $this->callback(function(IntegrationStatus $status) {
                return $status->getState() === Client::INTEGRATION_PENDING
                    && strpos($status->getDescription(), 'scheduled') !== false
                    && $status->getTargetUrl() === null;
            }));

        $this->listener->onPullRequestUpdated($event);
    }

    public function testItSetsTheIntegrationStatusAsScheduledWhenAPrIsSynced()
    {
        $domainEvent = new Event\PullRequestSynced($this->pr, 'before sha', 'after sha');
        $event = new Event\DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($this->pr, $this->callback(function(IntegrationStatus $status) {
                return $status->getState() === Client::INTEGRATION_PENDING
                && strpos($status->getDescription(), 'scheduled') !== false
                && $status->getTargetUrl() === null;
            }));

        $this->listener->onPullRequestUpdated($event);
    }

    public function testItSetsTheIntegrationStatusAsStartedWhenAnInspectionStarts()
    {
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\InspectionStarted($inspection, $this->pr);
        $event = new Event\DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($this->pr, $this->callback(function(IntegrationStatus $status) {
                return $status->getState() === Client::INTEGRATION_PENDING
                && strpos($status->getDescription(), 'started') !== false
                && $status->getTargetUrl() !== null;
            }));

        $this->listener->onInspectionStarted($event);
    }

    public function testItDoesNothingIfTheRepositoryDisabledTheInspections()
    {
        $this->repoRepository = $this->getMockBuilder(Repository\Repositories::class)->getMock();
        $this->repository = $this->getMockBuilder(Entity\Github\Repository::class)->disableOriginalConstructor()->getMock();
        $this->listener = new PullRequestInspectionStatusListener($this->ghClientFactory, $this->repoRepository, $this->urlGenerator);

        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\InspectionStarted($inspection, $this->pr);
        $event = new Event\DomainEventWrapper($domainEvent);

        $this->repository->expects($this->any())
            ->method('isInspectionEnabled')
            ->will($this->returnValue(false));

        $this->repoRepository->expects($this->any())
            ->method('find')
            ->with('repository/identifier')
            ->will($this->returnValue($this->repository));

        $this->ghClient->expects($this->never())->method('setIntegrationStatus');

        $this->listener->onInspectionStarted($event);
    }

    public function testItSetsTheIntegrationStatusAsErroredWhenAnInspectionFails()
    {
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\InspectionFailed($inspection, $this->pr, new \Exception());
        $event = new Event\DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($this->pr, $this->callback(function(IntegrationStatus $status) {
                return $status->getState() === Client::INTEGRATION_ERROR
                && strpos($status->getDescription(), 'failed') !== false
                && $status->getTargetUrl() !== null;
            }));

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
            ->with($this->pr, $this->callback(function(IntegrationStatus $status) {
                return $status->getState() === Client::INTEGRATION_FAILURE
                && strpos($status->getDescription(), 'error(s)') !== false
                && $status->getTargetUrl() !== null;
            }));

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
            ->with($this->pr, $this->callback(function(IntegrationStatus $status) {
                return $status->getState() === Client::INTEGRATION_FAILURE
                && strpos($status->getDescription(), 'error(s)') !== false
                && $status->getTargetUrl() !== null;
            }));

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
            ->with($this->pr, $this->callback(function(IntegrationStatus $status) {
                return $status->getState() === Client::INTEGRATION_SUCCESS
                && strpos($status->getDescription(), 'successful') !== false
                && $status->getTargetUrl() !== null;
            }));

        $this->listener->onInspectionFinished($event);
    }
}
