<?php

namespace Tests\Regis\Application\Event;

use Regis\Application\Event;

class DomainEventWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testItJustWrapsADomainEvent()
    {
        $domainEvent = $this->getMockBuilder(Event::class)->getMock();

        $symfonyEvent = new Event\DomainEventWrapper($domainEvent);

        $this->assertSame($domainEvent, $symfonyEvent->getDomainEvent());
    }
}
