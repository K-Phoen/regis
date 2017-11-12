<?php

declare(strict_types=1);

namespace Regis\Kernel\Event;

use Symfony\Component\EventDispatcher\Event;

class DomainEventWrapper extends Event
{
    private $domainEvent;

    public function __construct($event)
    {
        $this->domainEvent = $event;
    }

    public function getDomainEvent()
    {
        return $this->domainEvent;
    }
}
