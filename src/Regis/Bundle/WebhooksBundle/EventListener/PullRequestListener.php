<?php

namespace Regis\Bundle\WebhooksBundle\EventListener;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Regis\Bundle\WebhooksBundle\Event\DomainEventWrapper;
use Regis\Domain\Event;

class PullRequestListener implements EventSubscriberInterface
{
    private $producer;

    public function __construct(ProducerInterface $producer)
    {
        $this->producer = $producer;
    }

    public static function getSubscribedEvents()
    {
        return [
            Event::PULL_REQUEST_OPENED => [
                ['onPullRequestUpdated', 0],
            ],
            Event::PULL_REQUEST_SYNCED => [
                ['onPullRequestUpdated', 0],
            ],
        ];
    }

    public function onPullRequestUpdated(DomainEventWrapper $event)
    {
        /** @var Event $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->producer->publish(serialize($domainEvent));
    }
}