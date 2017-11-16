<?php

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
