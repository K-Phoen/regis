<?php

declare(strict_types=1);

namespace Regis\Kernel\Event;

use Symfony\Component\EventDispatcher\Event;
use Regis\Kernel\Events as DomainEvent;

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
