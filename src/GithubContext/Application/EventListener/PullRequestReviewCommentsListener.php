<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\EventListener;

use League\Tactician\CommandBus;
use Regis\GithubContext\Domain\Repository\PullRequestInspections;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Regis\Kernel\Event;
use Regis\Kernel\Events;
use Regis\GithubContext\Application\Command;

class PullRequestReviewCommentsListener implements EventSubscriberInterface
{
    private $commandBus;
    private $inspectionsRepo;

    public function __construct(CommandBus $commandBus, PullRequestInspections $inspectionsRepo)
    {
        $this->commandBus = $commandBus;
        $this->inspectionsRepo = $inspectionsRepo;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::INSPECTION_FINISHED => 'onInspectionFinished',
        ];
    }

    public function onInspectionFinished(Event\DomainEventWrapper $event)
    {
        /** @var Event\InspectionFinished $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $inspection = $this->inspectionsRepo->find($domainEvent->getInspectionId());

        $this->commandBus->handle(new Command\Inspection\SendViolationsAsComments($inspection));
    }
}
