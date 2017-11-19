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

namespace Tests\Regis\AnalysisContext\Application\EventListener;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Application\EventListener\InspectionResultListener;
use Regis\AnalysisContext\Application\Event;
use Regis\AnalysisContext\Domain\Entity\Inspection;
use Regis\Kernel\Event\DomainEventWrapper;
use Tests\Regis\Helper\ObjectManipulationHelper;

class InspectionResultListenerTest extends TestCase
{
    use ObjectManipulationHelper;

    private $producer;
    private $listener;

    public function setUp()
    {
        $this->producer = $this->createMock(ProducerInterface::class);

        $this->listener = new InspectionResultListener($this->producer);
    }

    public function eventClassProvider()
    {
        return [
            [Event\InspectionFinished::class],
            [Event\InspectionStarted::class],
            [Event\InspectionFailed::class],
        ];
    }

    /**
     * @dataProvider eventClassProvider
     */
    public function testItListensToTheRightEvents(string $eventClass)
    {
        $listenedEvents = InspectionResultListener::getSubscribedEvents();

        $this->assertArrayHasKey($eventClass, $listenedEvents);
    }

    /**
     * @dataProvider eventClassProvider
     */
    public function testItPublishesAnEventWhenAnInspectionChangesItsStatus(string $eventClass)
    {
        $inspection = new Inspection();
        $this->setPrivateValue($inspection, 'id', 'inspection-id');
        $this->setPrivateValue($inspection, 'type', 'github_pr');

        $domainEvent = new $eventClass($inspection, new \Exception());
        $event = new DomainEventWrapper($domainEvent);

        $this->producer->expects($this->once())
            ->method('publish')
            ->with($this->callback(function ($payload) {
                $this->assertJson($payload);
                $this->assertJsonStringEqualsJsonString('{"inspection_id": "inspection-id"}', $payload);

                return true;
            }), 'analysis.github_pr.status');

        $this->listener->onInspectionStatus($event);
    }
}
