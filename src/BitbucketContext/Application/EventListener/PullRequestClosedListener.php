<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\EventListener;

use League\Tactician\CommandBus;
use Regis\Kernel\Event\DomainEventWrapper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Regis\BitbucketContext\Application\Event;
use Regis\BitbucketContext\Application\Command;

class PullRequestClosedListener implements EventSubscriberInterface
{
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public static function getSubscribedEvents()
    {
        return [
            Event\PullRequestRejected::class => 'onPullRequestClosed',
            Event\PullRequestMerged::class => 'onPullRequestClosed',
        ];
    }

    public function onPullRequestClosed(DomainEventWrapper $event)
    {
        /** @var Event\PullRequestRejected|Event\PullRequestMerged $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->commandBus->handle(new Command\Inspection\ClearViolationsCache($domainEvent->getPullRequest()));
    }
}
