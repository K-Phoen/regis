<?php

namespace Regis\Bundle\WebhooksBundle\EventListener;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Regis\Bundle\WebhooksBundle\Event\DomainEventWrapper;
use Regis\Domain\Event;
use Regis\Domain\Inspection\ViolationsCache;

class PullRequestListener implements EventSubscriberInterface
{
    private $producer;
    private $violationsCache;

    public function __construct(ProducerInterface $producer, ViolationsCache $violationsCache)
    {
        $this->producer = $producer;
        $this->violationsCache = $violationsCache;
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
        /** @var Event $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->producer->publish(serialize($domainEvent));
    }

    public function onPullRequestClosed(DomainEventWrapper $event)
    {
        /** @var Event\PullRequestClosed $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->violationsCache->clear($domainEvent->getPullRequest());
    }
}