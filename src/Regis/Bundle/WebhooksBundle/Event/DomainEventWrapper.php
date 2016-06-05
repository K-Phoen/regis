<?php

namespace Regis\Bundle\WebhooksBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Regis\Domain\Event as DomainEvent;

class DomainEventWrapper extends Event
{
    private $domainEvent;

    public function __construct(DomainEvent $event)
    {
        $this->domainEvent = $event;
    }

    public function getDomainEvent(): DomainEvent
    {
        return $this->domainEvent;
    }
}