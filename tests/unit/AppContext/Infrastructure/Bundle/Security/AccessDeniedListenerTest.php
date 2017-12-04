<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Tests\Regis\AppContext\Infrastructure\Bundle\AppBundle\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException as SymfonyAccessDenied;
use Regis\Kernel\Security\Exception\AccessDenied;
use Regis\AppContext\Infrastructure\Bundle\AppBundle\Security\AccessDeniedListener;

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
