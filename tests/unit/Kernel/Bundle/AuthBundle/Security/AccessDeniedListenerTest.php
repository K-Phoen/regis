<?php

namespace Tests\Regis\Kernel\Bundle\AuthBundle\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException as SymfonyAccessDenied;

use Regis\Kernel\Security\Exception\AccessDenied;
use Regis\Kernel\Bundle\AuthBundle\Security\AccessDeniedListener;

class AccessDeniedListenerTest extends TestCase
{
    public function testItReplacesAccessDeniedExceptionsBySymfonyOnes()
    {
        $exception = AccessDenied::forCommand(new \Datetime());
        $event = $this->createMock(GetResponseForExceptionEvent::class);

        $event->expects($this->once())
            ->method('getException')
            ->willReturn($exception);

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
        $event = $this->createMock(GetResponseForExceptionEvent::class);

        $event->expects($this->once())
            ->method('getException')
            ->willReturn($exception);

        $event->expects($this->never())->method('setException');

        $listener = new AccessDeniedListener();
        $listener->onKernelException($event);
    }
}
