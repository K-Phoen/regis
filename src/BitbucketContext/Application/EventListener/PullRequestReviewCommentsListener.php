<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\EventListener;

use League\Tactician\CommandBus;
use Regis\Kernel\Event\DomainEventWrapper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Application\Event;

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
            Event\InspectionFinished::class => 'onInspectionFinished',
        ];
    }

    public function onInspectionFinished(DomainEventWrapper $event)
    {
        /** @var Event\InspectionFinished $domainEvent */
        $domainEvent = $event->getDomainEvent();
        $inspection = $domainEvent->getInspection();

        $this->commandBus->handle(new Command\Inspection\SendViolationsAsComments($inspection));
    }
}
