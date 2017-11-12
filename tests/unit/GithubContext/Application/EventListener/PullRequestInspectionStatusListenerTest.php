<?php

namespace Tests\Regis\GithubContext\Application\EventListener;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Application\Github\IntegrationStatus;
use Regis\GithubContext\Domain\Model\RepositoryIdentifier;
use Regis\Kernel\Event\DomainEventWrapper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as UrlGenerator;
use Regis\GithubContext\Application\Event;
use Regis\GithubContext\Application\EventListener\PullRequestInspectionStatusListener;
use Regis\GithubContext\Application\Github\Client;
use Regis\GithubContext\Application\Github\ClientFactory;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Model\PullRequest;
use Regis\GithubContext\Domain\Repository;

class PullRequestInspectionStatusListenerTest extends TestCase
{
    const INSPECTION_HEAD = 'inspection HEAD sha';
    const INSPECTION_URL = 'inspection-url';

    const WITH_ERRORS = 1;
    const WITH_WARNINGS = 2;

    /** @var ClientFactory */
    private $ghClientFactory;
    /** @var Client */
    private $ghClient;
    /** @var UrlGenerator */
    private $urlGenerator;
    /** @var Repository\Repositories */
    private $repoRepository;
    /** @var Entity\Repository */
    private $repositoryEntity;
    /** @var RepositoryIdentifier */
    private $repositoryIdentifier;
    /** @var PullRequest */
    private $pr;
    /** @var PullRequestInspectionStatusListener */
    private $listener;

    public function setUp()
    {
        $this->ghClientFactory = $this->createMock(ClientFactory::class);
        $this->ghClient = $this->createMock(Client::class);
        $this->repoRepository = $this->createMock(Repository\Repositories::class);
        $this->pr = $this->createMock(PullRequest::class);
        $this->repositoryEntity = $this->createMock(Entity\Repository::class);
        $this->urlGenerator = $this->createMock(UrlGenerator::class);

        $this->repositoryIdentifier = RepositoryIdentifier::fromFullName('repository/identifier');

        $this->repositoryEntity
            ->method('isInspectionEnabled')
            ->willReturn(true);
        $this->repositoryEntity
            ->method('toIdentifier')
            ->willReturn($this->repositoryIdentifier);

        $this->urlGenerator
            ->method('generate')
            ->willReturn(self::INSPECTION_URL);

        $this->pr
            ->method('getRepositoryIdentifier')
            ->willReturn($this->repositoryIdentifier);
        $this->pr
            ->method('getHead')
            ->willReturn(self::INSPECTION_HEAD);

        $this->repoRepository
            ->method('find')
            ->with($this->repositoryIdentifier->getIdentifier())
            ->willReturn($this->repositoryEntity);

        $this->ghClientFactory
            ->method('createForRepository')
            ->with($this->repositoryEntity)
            ->willReturn($this->ghClient);

        $this->listener = new PullRequestInspectionStatusListener($this->ghClientFactory, $this->repoRepository, $this->urlGenerator);
    }

    public function testItListensToTheRightEvents()
    {
        $listenedEvents = PullRequestInspectionStatusListener::getSubscribedEvents();

        $this->assertArrayHasKey(Event\PullRequestOpened::class, $listenedEvents);
        $this->assertArrayHasKey(Event\PullRequestSynced::class, $listenedEvents);

        $this->assertArrayHasKey(Event\InspectionStarted::class, $listenedEvents);
        $this->assertArrayHasKey(Event\InspectionFailed::class, $listenedEvents);
        $this->assertArrayHasKey(Event\InspectionFinished::class, $listenedEvents);
    }

    public function testItSetsTheIntegrationStatusAsScheduledWhenAPrIsOpened()
    {
        $domainEvent = new Event\PullRequestOpened($this->pr);
        $event = new DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with(
                $this->repositoryIdentifier,
                self::INSPECTION_HEAD,
                $this->callback(function (IntegrationStatus $status) {
                    $this->assertSame(Client::INTEGRATION_PENDING, $status->getState());
                    $this->assertContains('scheduled', $status->getDescription());
                    $this->assertNull($status->getTargetUrl());

                    return true;
                })
            );

        $this->listener->onPullRequestUpdated($event);
    }

    public function testItSetsTheIntegrationStatusAsScheduledWhenAPrIsSynced()
    {
        $domainEvent = new Event\PullRequestSynced($this->pr, 'before sha', 'after sha');
        $event = new DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with(
                $this->repositoryIdentifier,
                self::INSPECTION_HEAD,
                $this->callback(function (IntegrationStatus $status) {
                    $this->assertSame(Client::INTEGRATION_PENDING, $status->getState());
                    $this->assertContains('scheduled', $status->getDescription());
                    $this->assertNull($status->getTargetUrl());

                    return true;
                })
            );

        $this->listener->onPullRequestUpdated($event);
    }

    public function testItSetsTheIntegrationStatusAsStartedWhenAnInspectionStarts()
    {
        $inspection = $this->createInspection();
        $domainEvent = new Event\InspectionStarted($inspection);
        $event = new DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with(
                $this->repositoryIdentifier,
                self::INSPECTION_HEAD,
                $this->callback(function (IntegrationStatus $status) {
                    $this->assertSame(Client::INTEGRATION_PENDING, $status->getState());
                    $this->assertContains('started', $status->getDescription());
                    $this->assertSame(self::INSPECTION_URL, $status->getTargetUrl());

                    return true;
                })
            );

        $this->listener->onInspectionStarted($event);
    }

    public function testItDoesNothingIfTheRepositoryDisabledTheInspections()
    {
        $inspection = $this->createMock(Entity\PullRequestInspection::class);
        $repository = $this->createMock(Entity\Repository::class);
        $domainEvent = new Event\InspectionStarted($inspection);
        $event = new DomainEventWrapper($domainEvent);

        $inspection->method('getRepository')->willReturn($repository);
        $repository->method('isInspectionEnabled')->willReturn(false);

        $this->ghClient->expects($this->never())->method('setIntegrationStatus');

        $this->listener->onInspectionStarted($event);
    }

    public function testItSetsTheIntegrationStatusAsErroredWhenAnInspectionFails()
    {
        $inspection = $this->createInspection();
        $domainEvent = new Event\InspectionFailed($inspection);
        $event = new DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with(
                $this->repositoryIdentifier,
                self::INSPECTION_HEAD,
                $this->callback(function (IntegrationStatus $status) {
                    $this->assertSame(Client::INTEGRATION_ERROR, $status->getState());
                    $this->assertContains('failed', $status->getDescription());
                    $this->assertSame(self::INSPECTION_URL, $status->getTargetUrl());

                    return true;
                })
            );

        $this->listener->onInspectionFailed($event);
    }

    public function testItSetsTheIntegrationStatusAsFailedWhenAnInspectionFinishesWithErrors()
    {
        $inspection = $this->createInspection(self::WITH_ERRORS);
        $domainEvent = new Event\InspectionFinished($inspection);
        $event = new DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with(
                $this->repositoryIdentifier,
                self::INSPECTION_HEAD,
                $this->callback(function (IntegrationStatus $status) {
                    $this->assertSame(Client::INTEGRATION_FAILURE, $status->getState());
                    $this->assertContains('error(s)', $status->getDescription());
                    $this->assertSame(self::INSPECTION_URL, $status->getTargetUrl());

                    return true;
                })
            );

        $this->listener->onInspectionFinished($event);
    }

    public function testItSetsTheIntegrationStatusAsFailedWhenAnInspectionFinishesWithWarnings()
    {
        $inspection = $this->createInspection(self::WITH_WARNINGS);
        $domainEvent = new Event\InspectionFinished($inspection);
        $event = new DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with(
                $this->repositoryIdentifier,
                self::INSPECTION_HEAD,
                $this->callback(function (IntegrationStatus $status) {
                    $this->assertSame(Client::INTEGRATION_FAILURE, $status->getState());
                    $this->assertContains('error(s)', $status->getDescription());
                    $this->assertSame(self::INSPECTION_URL, $status->getTargetUrl());

                    return true;
                })
            );

        $this->listener->onInspectionFinished($event);
    }

    public function testItSetsTheIntegrationStatusAsSuccessWhenAnInspectionFinishesSuccessfully()
    {
        $inspection = $this->createInspection();
        $domainEvent = new Event\InspectionFinished($inspection);
        $event = new DomainEventWrapper($domainEvent);

        $this->ghClient->expects($this->once())
            ->method('setIntegrationStatus')
            ->with(
                $this->repositoryIdentifier,
                self::INSPECTION_HEAD,
                $this->callback(function (IntegrationStatus $status) {
                    $this->assertSame(Client::INTEGRATION_SUCCESS, $status->getState());
                    $this->assertContains('successful', $status->getDescription());
                    $this->assertSame(self::INSPECTION_URL, $status->getTargetUrl());

                    return true;
                })
            );

        $this->listener->onInspectionFinished($event);
    }

    private function createInspection(int $flags = 0): Entity\PullRequestInspection
    {
        $inspection = $this->createMock(Entity\PullRequestInspection::class);
        $report = $this->createReport($flags);

        $inspection->method('getHead')->willReturn(self::INSPECTION_HEAD);
        $inspection->method('getRepository')->willReturn($this->repositoryEntity);
        $inspection->method('hasReport')->willReturn(true);
        $inspection->method('getReport')->willReturn($report);

        return $inspection;
    }

    private function createReport(int $flags = 0): Entity\Report
    {
        $report = $this->createMock(Entity\Report::class);

        if (($flags & self::WITH_ERRORS) !== 0) {
            $report->method('hasErrors')->willReturn(true);
        }

        if (($flags & self::WITH_WARNINGS) !== 0) {
            $report->method('hasWarnings')->willReturn(true);
        }

        return $report;
    }
}
