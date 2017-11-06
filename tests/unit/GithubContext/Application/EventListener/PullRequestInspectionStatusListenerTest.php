<?php

namespace Tests\Regis\GithubContext\Application\EventListener;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Application\Github\IntegrationStatus;
use Regis\Kernel\Event\DomainEventWrapper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as UrlGenerator;
use Regis\GithubContext\Application\Event;
use Regis\GithubContext\Application\EventListener\PullRequestInspectionStatusListener;
use Regis\GithubContext\Application\Github\Client;
use Regis\GithubContext\Application\Github\ClientFactory;
use Regis\GithubContext\Application\Events;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Model\PullRequest;
use Regis\GithubContext\Domain\Repository;
use Regis\Kernel\Events as KernelEvents;
use Regis\Kernel\Event as KernelEvent;

class PullRequestInspectionStatusListenerTest extends TestCase
{
    /** @var ClientFactory */
    private $ghClientFactory;
    /** @var Client */
    private $ghClient;
    /** @var UrlGenerator */
    private $urlGenerator;
    /** @var Repository\Repositories */
    private $repoRepository;
    /** @var Repository\PullRequestInspections */
    private $prInspectionsRepo;
    /** @var Entity\Repository */
    private $repository;
    /** @var PullRequest */
    private $pr;
    /** @var PullRequestInspectionStatusListener */
    private $listener;

    public function setUp()
    {
        $this->ghClientFactory = $this->createMock(ClientFactory::class);
        $this->ghClient = $this->createMock(Client::class);
        $this->repoRepository = $this->createMock(Repository\Repositories::class);
        $this->prInspectionsRepo = $this->createMock(Repository\PullRequestInspections::class);
        $this->pr = $this->createMock(PullRequest::class);
        $this->repository = $this->createMock(Entity\Repository::class);
        $this->urlGenerator = $this->createMock(UrlGenerator::class);

        $this->repository
            ->method('isInspectionEnabled')
            ->willReturn(true);

        $this->urlGenerator
            ->method('generate')
            ->willReturn('something not null');

        $this->pr
            ->method('getRepositoryIdentifier')
            ->willReturn('repository/identifier');

        $this->repoRepository
            ->method('find')
            ->with('repository/identifier')
            ->willReturn($this->repository);

        $this->ghClientFactory
            ->method('createForRepository')
            ->with($this->repository)
            ->willReturn($this->ghClient);

        $this->listener = new PullRequestInspectionStatusListener($this->ghClientFactory, $this->repoRepository, $this->prInspectionsRepo, $this->urlGenerator);
    }

    public function testItListensToTheRightEvents()
    {
        $listenedEvents = PullRequestInspectionStatusListener::getSubscribedEvents();

        $this->assertArrayHasKey(Events::PULL_REQUEST_OPENED, $listenedEvents);
        $this->assertArrayHasKey(Events::PULL_REQUEST_SYNCED, $listenedEvents);

        $this->assertArrayHasKey(KernelEvents::INSPECTION_STARTED, $listenedEvents);
        $this->assertArrayHasKey(KernelEvents::INSPECTION_FAILED, $listenedEvents);
        $this->assertArrayHasKey(KernelEvents::INSPECTION_FINISHED, $listenedEvents);
    }

    public function testItSetsTheIntegrationStatusAsScheduledWhenAPrIsOpened()
    {
        $domainEvent = new Event\PullRequestOpened($this->pr);
        $event = new DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($this->pr, $this->callback(function (IntegrationStatus $status) {
                return $status->getState() === Client::INTEGRATION_PENDING
                    && strpos($status->getDescription(), 'scheduled') !== false
                    && $status->getTargetUrl() === null;
            }));

        $this->listener->onPullRequestUpdated($event);
    }

    public function testItSetsTheIntegrationStatusAsScheduledWhenAPrIsSynced()
    {
        $domainEvent = new Event\PullRequestSynced($this->pr, 'before sha', 'after sha');
        $event = new DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($this->pr, $this->callback(function (IntegrationStatus $status) {
                return $status->getState() === Client::INTEGRATION_PENDING
                && strpos($status->getDescription(), 'scheduled') !== false
                && $status->getTargetUrl() === null;
            }));

        $this->listener->onPullRequestUpdated($event);
    }

    public function testItSetsTheIntegrationStatusAsStartedWhenAnInspectionStarts()
    {
        $inspection = $this->getMockBuilder(PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new KernelEvent\InspectionStarted($inspection, $this->pr);
        $event = new DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($this->pr, $this->callback(function (IntegrationStatus $status) {
                return $status->getState() === Client::INTEGRATION_PENDING
                && strpos($status->getDescription(), 'started') !== false
                && $status->getTargetUrl() !== null;
            }));

        $this->listener->onInspectionStarted($event);
    }

    public function testItDoesNothingIfTheRepositoryDisabledTheInspections()
    {
        $this->repoRepository = $this->getMockBuilder(Repository\Repositories::class)->getMock();
        $this->repository = $this->getMockBuilder(Entity\Repository::class)->disableOriginalConstructor()->getMock();
        $this->listener = new PullRequestInspectionStatusListener($this->ghClientFactory, $this->repoRepository, $this->prInspectionsRepo, $this->urlGenerator);

        $inspection = $this->getMockBuilder(Entity\PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new KernelEvent\InspectionStarted($inspection, $this->pr);
        $event = new DomainEventWrapper($domainEvent);

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
        $domainEvent = new KernelEvent\InspectionFailed($inspection, $this->pr, new \Exception());
        $event = new DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($this->pr, $this->callback(function (IntegrationStatus $status) {
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
        $domainEvent = new KernelEvent\InspectionFinished($inspection, $this->pr, $report);
        $event = new DomainEventWrapper($domainEvent);

        $report->expects($this->once())
            ->method('hasErrors')
            ->will($this->returnValue(true));

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($this->pr, $this->callback(function (IntegrationStatus $status) {
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
        $domainEvent = new KernelEvent\InspectionFinished($inspection, $this->pr, $report);
        $event = new DomainEventWrapper($domainEvent);

        $report->expects($this->once())
            ->method('hasWarnings')
            ->will($this->returnValue(true));

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($this->pr, $this->callback(function (IntegrationStatus $status) {
                return $status->getState() === Client::INTEGRATION_FAILURE
                && strpos($status->getDescription(), 'error(s)') !== false
                && $status->getTargetUrl() !== null;
            }));

        $this->listener->onInspectionFinished($event);
    }

    public function testItSetsTheIntegrationStatusAsSuccessWhenAnInspectionFinishesSuccessfully()
    {
        $inspection = $this->getMockBuilder(Entity\PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $report = $this->getMockBuilder(Entity\Report::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new KernelEvent\InspectionFinished($inspection, $this->pr, $report);
        $event = new DomainEventWrapper($domainEvent);

        $report->expects($this->once())
            ->method('hasErrors')
            ->will($this->returnValue(false));
        $report->expects($this->once())
            ->method('hasWarnings')
            ->will($this->returnValue(false));

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with($this->pr, $this->callback(function (IntegrationStatus $status) {
                return $status->getState() === Client::INTEGRATION_SUCCESS
                && strpos($status->getDescription(), 'successful') !== false
                && $status->getTargetUrl() !== null;
            }));

        $this->listener->onInspectionFinished($event);
    }
}
