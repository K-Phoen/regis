<?php

namespace Regis\Bundle\WebhooksBundle\EventListener;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Regis\Bundle\WebhooksBundle\Event\DomainEventWrapper;
use Regis\Domain\Event\PullRequestOpened;
use Regis\Domain\Events;

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
            Events::PULL_REQUEST_OPENED => [
                ['onPullRequestOpened', 0],
            ],
        ];
    }

    public function onPullRequestOpened(DomainEventWrapper $event)
    {
        /** @var PullRequestOpened $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->producer->publish(serialize($domainEvent));
    }
}