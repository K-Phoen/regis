<?php

namespace Regis\Bundle\WebhooksBundle\EventListener;

use League\Tactician\CommandBus;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Regis\Application\Command;
use Regis\Application\Event;
use Regis\Application\Inspection\ViolationsCache;
use Regis\Symfony\Event\DomainEventWrapper;

class PullRequestListener implements EventSubscriberInterface
{
    private $commandBus;
    private $violationsCache;

    public function __construct(CommandBus $commandBus, ViolationsCache $violationsCache)
    {
        $this->violationsCache = $violationsCache;
        $this->commandBus = $commandBus;
    }

    public static function getSubscribedEvents()
    {
        return [
            Event::PULL_REQUEST_OPENED => 'onPullRequestUpdated',
            Event::PULL_REQUEST_SYNCED => 'onPullRequestUpdated',
            Event::PULL_REQUEST_CLOSED => 'onPullRequestClosed',
        ];
    }

    public function onPullRequestUpdated(DomainEventWrapper $event)
    {
        /** @var Event\PullRequestOpened|Event\PullRequestSynced $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $command = new Command\Github\Inspection\SchedulePullRequest($domainEvent->getPullRequest());
        $this->commandBus->handle($command);
    }

    public function onPullRequestClosed(DomainEventWrapper $event)
    {
        /** @var Event\PullRequestClosed $domainEvent */
        $domainEvent = $event->getDomainEvent();

        // TODO should be in a command
        $this->violationsCache->clear($domainEvent->getPullRequest());
    }
}