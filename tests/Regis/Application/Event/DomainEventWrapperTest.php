<?php

namespace Tests\Regis\Application\Event;

use PHPUnit\Framework\TestCase;
use Regis\Application\Event;

class DomainEventWrapperTest extends TestCase
{
    public function testItJustWrapsADomainEvent()
    {
        $domainEvent = $this->getMockBuilder(Event::class)->getMock();

        $symfonyEvent = new Event\DomainEventWrapper($domainEvent);

        $this->assertSame($domainEvent, $symfonyEvent->getDomainEvent());
    }
}
