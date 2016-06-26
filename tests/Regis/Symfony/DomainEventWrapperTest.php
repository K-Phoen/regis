<?php

namespace Tests\Regis\Symfony;

use Regis\Application\Event;
use Regis\Symfony\Event\DomainEventWrapper;

class DomainEventWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testItJustWrapsADomainEvent()
    {
        $domainEvent = $this->getMockBuilder(Event::class)->getMock();

        $symfonyEvent = new DomainEventWrapper($domainEvent);

        $this->assertSame($domainEvent, $symfonyEvent->getDomainEvent());
    }
}
