<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Tests\Regis\BitbucketContext\Application\EventListener;

use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Domain\Model\RepositoryIdentifier;
use Regis\Kernel\Event\DomainEventWrapper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as UrlGenerator;
use Regis\BitbucketContext\Application\Event;
use Regis\BitbucketContext\Application\EventListener\PullRequestBuildStatusListener;
use Regis\BitbucketContext\Application\Bitbucket\BuildStatus;
use Regis\BitbucketContext\Application\Bitbucket\Client;
use Regis\BitbucketContext\Application\Bitbucket\ClientFactory;
use Regis\BitbucketContext\Domain\Entity;

class PullRequestBuildStatusListenerTest extends TestCase
{
    private const INSPECTION_HEAD = 'inspection HEAD sha';
    private const INSPECTION_URL = 'inspection-url';

    private const WITH_ERRORS = 1;
    private const WITH_WARNINGS = 2;
    private const WITH_NO_REPORT = 4;

    /** @var ClientFactory */
    private $bitbucketClientFactory;
    /** @var Client */
    private $bitbucketClient;
    /** @var UrlGenerator */
    private $urlGenerator;
    /** @var Entity\Repository */
    private $repositoryEntity;
    /** @var RepositoryIdentifier */
    private $repositoryIdentifier;
    /** @var PullRequestBuildStatusListener */
    private $listener;

    public function setUp()
    {
        $this->bitbucketClientFactory = $this->createMock(ClientFactory::class);
        $this->bitbucketClient = $this->createMock(Client::class);
        $this->repositoryEntity = $this->createMock(Entity\Repository::class);
        $this->urlGenerator = $this->createMock(UrlGenerator::class);

        $this->repositoryIdentifier = new RepositoryIdentifier('repository/identifier');

        $this->repositoryEntity
            ->method('isInspectionEnabled')
            ->willReturn(true);
        $this->repositoryEntity
            ->method('toIdentifier')
            ->willReturn($this->repositoryIdentifier);

        $this->urlGenerator
            ->method('generate')
            ->willReturn(self::INSPECTION_URL);

        $this->bitbucketClientFactory
            ->method('createForRepository')
            ->with($this->repositoryEntity)
            ->willReturn($this->bitbucketClient);

        $this->listener = new PullRequestBuildStatusListener($this->bitbucketClientFactory, $this->urlGenerator);
    }

    public function testItListensToTheRightEvents()
    {
        $listenedEvents = PullRequestBuildStatusListener::getSubscribedEvents();

        $this->assertArrayHasKey(Event\InspectionStarted::class, $listenedEvents);
        $this->assertArrayHasKey(Event\InspectionFailed::class, $listenedEvents);
        $this->assertArrayHasKey(Event\InspectionFinished::class, $listenedEvents);
    }

    public function testItSetsTheIntegrationStatusAsStartedWhenAnInspectionStarts()
    {
        $inspection = $this->createInspection();
        $domainEvent = new Event\InspectionStarted($inspection);
        $event = new DomainEventWrapper($domainEvent);

        $this->bitbucketClient->expects($this->once())
            ->method('setBuildStatus')
            ->with(
                $this->repositoryIdentifier,
                $this->callback(function (BuildStatus $status) {
                    $this->assertSame(BuildStatus::STATE_INPROGRESS, $status->state());
                    $this->assertContains('started', $status->description());
                    $this->assertSame(self::INSPECTION_URL, $status->url());

                    return true;
                }),
                self::INSPECTION_HEAD
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

        $this->bitbucketClient->expects($this->never())->method('setBuildStatus');

        $this->listener->onInspectionStarted($event);
    }

    public function testItDoesNothingIfTheRepositoryIsInFlightMode()
    {
        $inspection = $this->createMock(Entity\PullRequestInspection::class);
        $repository = $this->createMock(Entity\Repository::class);
        $domainEvent = new Event\InspectionStarted($inspection);
        $event = new DomainEventWrapper($domainEvent);

        $inspection->method('getRepository')->willReturn($repository);
        $repository->method('isInspectionEnabled')->willReturn(true);
        $repository->method('isFlightModeEnabled')->willReturn(true);

        $this->bitbucketClient->expects($this->never())->method('setBuildStatus');

        $this->listener->onInspectionStarted($event);
    }

    public function testItSetsTheIntegrationStatusAsErroredWhenAnInspectionFails()
    {
        $inspection = $this->createInspection();
        $domainEvent = new Event\InspectionFailed($inspection);
        $event = new DomainEventWrapper($domainEvent);

        $this->bitbucketClient->expects($this->once())
            ->method('setBuildStatus')
            ->with(
                $this->repositoryIdentifier,
                $this->callback(function (BuildStatus $status) {
                    $this->assertSame(BuildStatus::STATE_FAILED, $status->state());
                    $this->assertContains('failed', $status->description());
                    $this->assertSame(self::INSPECTION_URL, $status->url());

                    return true;
                }),
                self::INSPECTION_HEAD
            );

        $this->listener->onInspectionFailed($event);
    }

    public function testItSetsTheIntegrationStatusAsErroredWhenThereIsNoInspectionReport()
    {
        $inspection = $this->createInspection(self::WITH_NO_REPORT);
        $domainEvent = new Event\InspectionFailed($inspection);
        $event = new DomainEventWrapper($domainEvent);

        $this->bitbucketClient->expects($this->once())
            ->method('setBuildStatus')
            ->with(
                $this->repositoryIdentifier,
                $this->callback(function (BuildStatus $status) {
                    $this->assertSame(BuildStatus::STATE_FAILED, $status->state());
                    $this->assertContains('Internal error', $status->description());
                    $this->assertSame(self::INSPECTION_URL, $status->url());

                    return true;
                }),
                self::INSPECTION_HEAD
            );

        $this->listener->onInspectionFinished($event);
    }

    public function testItSetsTheIntegrationStatusAsFailedWhenAnInspectionFinishesWithErrors()
    {
        $inspection = $this->createInspection(self::WITH_ERRORS);
        $domainEvent = new Event\InspectionFinished($inspection);
        $event = new DomainEventWrapper($domainEvent);

        $this->bitbucketClient->expects($this->once())
            ->method('setBuildStatus')
            ->with(
                $this->repositoryIdentifier,
                $this->callback(function (BuildStatus $status) {
                    $this->assertSame(BuildStatus::STATE_FAILED, $status->state());
                    $this->assertContains('error(s)', $status->description());
                    $this->assertSame(self::INSPECTION_URL, $status->url());

                    return true;
                }),
                self::INSPECTION_HEAD
            );

        $this->listener->onInspectionFinished($event);
    }

    public function testItSetsTheIntegrationStatusAsFailedWhenAnInspectionFinishesWithWarnings()
    {
        $inspection = $this->createInspection(self::WITH_WARNINGS);
        $domainEvent = new Event\InspectionFinished($inspection);
        $event = new DomainEventWrapper($domainEvent);

        $this->bitbucketClient->expects($this->once())
            ->method('setBuildStatus')
            ->with(
                $this->repositoryIdentifier,
                $this->callback(function (BuildStatus $status) {
                    $this->assertSame(BuildStatus::STATE_FAILED, $status->state());
                    $this->assertContains('error(s)', $status->description());
                    $this->assertSame(self::INSPECTION_URL, $status->url());

                    return true;
                }),
                self::INSPECTION_HEAD
            );

        $this->listener->onInspectionFinished($event);
    }

    public function testItSetsTheIntegrationStatusAsSuccessWhenAnInspectionFinishesSuccessfully()
    {
        $inspection = $this->createInspection();
        $domainEvent = new Event\InspectionFinished($inspection);
        $event = new DomainEventWrapper($domainEvent);

        $this->bitbucketClient->expects($this->once())
            ->method('setBuildStatus')
            ->with(
                $this->repositoryIdentifier,
                $this->callback(function (BuildStatus $status) {
                    $this->assertSame(BuildStatus::STATE_SUCCESSFUL, $status->state());
                    $this->assertContains('successful', $status->description());
                    $this->assertSame(self::INSPECTION_URL, $status->url());

                    return true;
                }),
                self::INSPECTION_HEAD
            );

        $this->listener->onInspectionFinished($event);
    }

    private function createInspection(int $flags = 0): Entity\PullRequestInspection
    {
        $inspection = $this->createMock(Entity\PullRequestInspection::class);

        $inspection->method('getHead')->willReturn(self::INSPECTION_HEAD);
        $inspection->method('getRepository')->willReturn($this->repositoryEntity);

        if (($flags & self::WITH_NO_REPORT) === 0) {
            $report = $this->createReport($flags);

            $inspection->method('hasReport')->willReturn(true);
            $inspection->method('getReport')->willReturn($report);
        } else {
            $inspection->method('hasReport')->willReturn(false);
        }

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
