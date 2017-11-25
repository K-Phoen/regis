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

namespace Tests\Regis\Kernel\Infrastructure\Worker;

use PHPUnit\Framework\TestCase;
use Regis\Kernel\Infrastructure\Worker\SwarrotMessagePublisher;
use Regis\Kernel\Worker\Message;
use Swarrot\SwarrotBundle\Broker\Publisher;
use Swarrot\Broker\Message as SwarrotMessage;

class SwarrotMessagePublisherTest extends TestCase
{
    private $swarrotPublisher;
    private $publisher;

    public function setUp()
    {
        $this->swarrotPublisher = $this->createMock(Publisher::class);

        $this->publisher = new SwarrotMessagePublisher($this->swarrotPublisher);
    }

    public function testItCanScheduleInspections()
    {
        $this->swarrotPublisher->expects($this->once())
            ->method('publish')
            ->with(Message::TYPE_ANALYSIS_INSPECTION, $this->callback(function (SwarrotMessage $message) {
                $this->assertJsonStringEqualsJsonString('{"foo":"bar"}', $message->getBody());

                return true;
            }));

        $this->publisher->scheduleInspection(['foo' => 'bar']);
    }

    public function testItcanNotifyThatAnInspectionIsOver()
    {
        $this->swarrotPublisher->expects($this->once())
            ->method('publish')
            ->with(Message::TYPE_ANALYSIS_STATUS, $this->callback(function (SwarrotMessage $message) {
                $this->assertJsonStringEqualsJsonString('{"inspection_id":"inspection-id"}', $message->getBody());

                return true;
            }), ['routing_key' => 'analysis.inspection-type.status']);

        $this->publisher->notifyInspectionOver('inspection-id', 'inspection-type');
    }
}
