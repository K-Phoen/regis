<?php

declare(strict_types=1);

namespace Tests\Regis\Kernel\Event;

use PHPUnit\Framework\TestCase;
use Regis\Kernel\Event;

class DomainEventWrapperTest extends TestCase
{
    public function testItJustWrapsADomainEvent()
    {
        $domainEvent = new \stdClass();

        $symfonyEvent = new Event\DomainEventWrapper($domainEvent);

        $this->assertInstanceOf(\Symfony\Component\EventDispatcher\Event::class, $symfonyEvent);
        $this->assertSame($domainEvent, $symfonyEvent->getDomainEvent());
    }
}
