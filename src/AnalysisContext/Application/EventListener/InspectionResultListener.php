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

namespace Regis\AnalysisContext\Application\EventListener;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Regis\Kernel\Event as KernelEvent;
use Regis\AnalysisContext\Application\Event;

class InspectionResultListener implements EventSubscriberInterface
{
    private $producer;

    public function __construct(ProducerInterface $producer)
    {
        $this->producer = $producer;
    }

    public static function getSubscribedEvents()
    {
        return [
            Event\InspectionStarted::class => 'onInspectionStatus',
            Event\InspectionFinished::class => 'onInspectionStatus',
            Event\InspectionFailed::class => 'onInspectionStatus',
        ];
    }

    public function onInspectionStatus(KernelEvent\DomainEventWrapper $event)
    {
        /** @var Event\InspectionStarted|Event\InspectionFinished|Event\InspectionFailed $domainEvent */
        $domainEvent = $event->getDomainEvent();
        $inspection = $domainEvent->getInspection();

        $routingKey = sprintf('analysis.%s.status', $inspection->type());

        $this->producer->publish(json_encode([
            'inspection_id' => $inspection->id(),
        ]), $routingKey);
    }
}
