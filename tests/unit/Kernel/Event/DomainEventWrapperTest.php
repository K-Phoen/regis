<?php

namespace Tests\Regis\Kernel\Event;

use PHPUnit\Framework\TestCase;
use Regis\Kernel\Events;
use Regis\Kernel\Event;

class DomainEventWrapperTest extends TestCase
{
    public function testItJustWrapsADomainEvent()
    {
        $domainEvent = $this->createMock(Events::class);

        $symfonyEvent = new Event\DomainEventWrapper($domainEvent);

        $this->assertSame($domainEvent, $symfonyEvent->getDomainEvent());
    }
}
