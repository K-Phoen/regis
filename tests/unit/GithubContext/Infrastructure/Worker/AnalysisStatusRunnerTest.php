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

namespace Tests\Regis\GithubContext\Infrastructure\Worker;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Infrastructure\Worker\AnalysisStatusRunner;
use Regis\GithubContext\Domain\Entity\PullRequestInspection;
use Swarrot\Broker\Message;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Regis\Kernel\Event\DomainEventWrapper;
use Regis\GithubContext\Domain\Entity\Inspection;
use Regis\GithubContext\Domain\Repository\PullRequestInspections;
use Regis\GithubContext\Application\Event;

class AnalysisStatusRunnerTest extends TestCase
{
    private const INSPECTION_ID = 'inspection-id';

    private $inspectionsRepo;
    private $dispatcher;
    private $worker;

    public function setUp()
    {
        $this->inspectionsRepo = $this->createMock(PullRequestInspections::class);
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->worker = new AnalysisStatusRunner($this->inspectionsRepo, $this->dispatcher);
    }

    public function testItConvertsInspectionStartedEvents()
    {
        $message = $this->message(self::INSPECTION_ID);
        $inspection = $this->inspection(Inspection::STATUS_STARTED);

        $this->inspectionsRepo->method('find')->with(self::INSPECTION_ID)->willReturn($inspection);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(Event\InspectionStarted::class, $this->callback(function (DomainEventWrapper $event) use ($inspection) {
                $this->assertInstanceOf(Event\InspectionStarted::class, $event->getDomainEvent());
                $this->assertSame($inspection, $event->getDomainEvent()->getInspection());

                return true;
            }));

        $this->worker->process($message, []);
    }

    public function testItConvertsInspectionFinishedEvents()
    {
        $message = $this->message(self::INSPECTION_ID);
        $inspection = $this->inspection(Inspection::STATUS_FINISHED);

        $this->inspectionsRepo->method('find')->with(self::INSPECTION_ID)->willReturn($inspection);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(Event\InspectionFinished::class, $this->callback(function (DomainEventWrapper $event) use ($inspection) {
                $this->assertInstanceOf(Event\InspectionFinished::class, $event->getDomainEvent());
                $this->assertSame($inspection, $event->getDomainEvent()->getInspection());

                return true;
            }));

        $this->worker->process($message, []);
    }

    public function testItConvertsInspectionFailedEvents()
    {
        $message = $this->message(self::INSPECTION_ID);
        $inspection = $this->inspection(Inspection::STATUS_FAILED);

        $this->inspectionsRepo->method('find')->with(self::INSPECTION_ID)->willReturn($inspection);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(Event\InspectionFailed::class, $this->callback(function (DomainEventWrapper $event) use ($inspection) {
                $this->assertInstanceOf(Event\InspectionFailed::class, $event->getDomainEvent());
                $this->assertSame($inspection, $event->getDomainEvent()->getInspection());

                return true;
            }));

        $this->worker->process($message, []);
    }

    public function testItRaisesAnErrorForUnknownInspectionStatuses()
    {
        $message = $this->message(self::INSPECTION_ID);
        $inspection = $this->inspection('unknown status');

        $this->inspectionsRepo->method('find')->with(self::INSPECTION_ID)->willReturn($inspection);

        $this->dispatcher->expects($this->never())->method('dispatch');
        $this->expectException(\LogicException::class);

        $this->worker->process($message, []);
    }

    private function message(string $inspectionId): Message
    {
        return new Message(json_encode([
            'inspection_id' => $inspectionId,
        ]));
    }

    private function inspection(string $status): PullRequestInspection
    {
        $inspection = $this->createMock(PullRequestInspection::class);

        $inspection->method('getStatus')->willReturn($status);

        return $inspection;
    }
}
