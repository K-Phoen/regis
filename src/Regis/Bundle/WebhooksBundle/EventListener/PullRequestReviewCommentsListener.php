<?php

declare(strict_types=1);

namespace Regis\Bundle\WebhooksBundle\EventListener;

use League\Tactician\CommandBus;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Regis\Application\Event;
use Regis\Application\Command;
use Regis\Symfony\Event\DomainEventWrapper;

class PullRequestReviewCommentsListener implements EventSubscriberInterface
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

    public function onInspectionFinished(DomainEventWrapper $event)
    {
        /** @var Event\InspectionFinished $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->commandBus->handle(new Command\Github\Inspection\SendViolationsAsComments(
            $domainEvent->getInspection(),
            $domainEvent->getPullRequest()
        ));
    }
}