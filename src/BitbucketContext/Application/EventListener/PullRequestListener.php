<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\EventListener;

use League\Tactician\CommandBus;
use Regis\Kernel\Event\DomainEventWrapper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Regis\BitbucketContext\Application\Event;
use Regis\BitbucketContext\Application\Command;

class PullRequestListener implements EventSubscriberInterface
{
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public static function getSubscribedEvents()
    {
        return [
            Event\PullRequestOpened::class => 'onPullRequestUpdated',
            Event\PullRequestUpdated::class => 'onPullRequestUpdated',
        ];
    }

    public function onPullRequestUpdated(DomainEventWrapper $event)
    {
        /** @var Event\PullRequestOpened|Event\PullRequestUpdated $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $command = new Command\Inspection\SchedulePullRequest($domainEvent->getPullRequest());
        $this->commandBus->handle($command);
    }
}
