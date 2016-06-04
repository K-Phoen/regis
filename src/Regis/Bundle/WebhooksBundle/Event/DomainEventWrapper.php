<?php

namespace Regis\Bundle\WebhooksBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class DomainEventWrapper extends Event
{
    private $domainEvent;

    public function __construct($event)
    {
        $this->domainEvent = $event;
    }

    /**
     * @return mixed
     */
    public function getDomainEvent()
    {
        return $this->domainEvent;
    }
}