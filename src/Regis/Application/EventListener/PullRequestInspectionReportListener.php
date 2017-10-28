<?php

declare(strict_types=1);

namespace Regis\Application\EventListener;

use League\Tactician\CommandBus;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Regis\Application\Command;
use Regis\Application\Event;

class PullRequestInspectionReportListener implements EventSubscriberInterface
{
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public static function getSubscribedEvents()
    {
        return [
            Event::INSPECTION_FINISHED => 'onInspectionFinished',
        ];
    }

    public function onInspectionFinished(Event\DomainEventWrapper $event)
    {
        /** @var Event\InspectionFinished $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->commandBus->handle(new Command\Github\Inspection\SavePullRequestReport(
            $domainEvent->getInspection(),
            $domainEvent->getReport()
        ));
    }
}
