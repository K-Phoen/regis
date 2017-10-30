<?php

namespace Tests\Regis\Infrastructure\Bundle\AuthBundle\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException as SymfonyAccessDenied;

use Regis\Application\Security\Exception\AccessDenied;
use Regis\Infrastructure\Bundle\AuthBundle\Security\AccessDeniedListener;

class AccessDeniedListenerTest extends TestCase
{
    public function testItReplacesAccessDeniedExceptionsBySymfonyOnes()
    {
        $exception = AccessDenied::forCommand(new \Datetime());
        $event = $this->getMockBuilder(GetResponseForExceptionEvent::class)->disableOriginalConstructor()->getMock();

        $event->expects($this->once())
            ->method('getException')
            ->will($this->returnValue($exception));

        $event->expects($this->once())
            ->method('setException')
            ->with($this->callback(function ($exception) {
                return $exception instanceof SymfonyAccessDenied;
            }));

        $listener = new AccessDeniedListener();
        $listener->onKernelException($event);
    }

    public function testItDoesNothingForOtherExceptions()
    {
        $exception = new \RuntimeException();
        $event = $this->getMockBuilder(GetResponseForExceptionEvent::class)->disableOriginalConstructor()->getMock();

        $event->expects($this->once())
            ->method('getException')
            ->will($this->returnValue($exception));

        $event->expects($this->never())->method('setException');

        $listener = new AccessDeniedListener();
        $listener->onKernelException($event);
    }
}
