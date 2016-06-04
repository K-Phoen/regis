<?php

namespace Regis\Bundle\WebhooksBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Regis\Bundle\WebhooksBundle\Event\DomainEventWrapper;
use Regis\Domain\Event\PullRequestOpened;
use Regis\Domain\Events;
use Regis\Domain\Inspector;

class PullRequestListener implements EventSubscriberInterface
{
    private $inspector;

    public function __construct(Inspector $inspector)
    {
        $this->inspector = $inspector;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::PULL_REQUEST_OPENED => [
                ['onPullRequestOpened', 0],
            ],
        ];
    }

    public function onPullRequestOpened(DomainEventWrapper $event)
    {
        /** @var PullRequestOpened $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->inspector->inspectPullRequest($domainEvent->getPullRequest());
    }
}