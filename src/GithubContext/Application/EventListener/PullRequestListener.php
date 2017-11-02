<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\EventListener;

use League\Tactician\CommandBus;
use Regis\Kernel\Event\DomainEventWrapper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Regis\GithubContext\Application\Events as GithubEvents;
use Regis\GithubContext\Application\Event as GithubEvent;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\Inspection\ViolationsCache;

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
            GithubEvents::PULL_REQUEST_OPENED => 'onPullRequestUpdated',
            GithubEvents::PULL_REQUEST_SYNCED => 'onPullRequestUpdated',
            GithubEvents::PULL_REQUEST_CLOSED => 'onPullRequestClosed',
        ];
    }

    public function onPullRequestUpdated(DomainEventWrapper $event)
    {
        /** @var GithubEvent\PullRequestOpened|GithubEvent\PullRequestSynced $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $command = new Command\Inspection\SchedulePullRequest($domainEvent->getPullRequest());
        $this->commandBus->handle($command);
    }

    public function onPullRequestClosed(DomainEventWrapper $event)
    {
        /** @var GithubEvent\PullRequestClosed $domainEvent */
        $domainEvent = $event->getDomainEvent();

        // TODO should be in a command
        $this->violationsCache->clear($domainEvent->getPullRequest());
    }
}
