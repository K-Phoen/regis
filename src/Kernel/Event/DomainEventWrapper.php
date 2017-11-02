<?php

declare(strict_types=1);

namespace Regis\Kernel\Event;

use Symfony\Component\EventDispatcher\Event;
use Regis\Event as DomainEvent;

class DomainEventWrapper extends Event
{
    private $domainEvent;

    public function __construct(DomainEvent\Events $event)
    {
        $this->domainEvent = $event;
    }

    public function getDomainEvent(): DomainEvent\Events
    {
        return $this->domainEvent;
    }
}
