<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Bundle\AuthBundle\Security;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException as SymfonyAccessDenied;

use Regis\Application\Security\Exception\AccessDenied as RegisAccessDenied;

class AccessDeniedListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof RegisAccessDenied) {
            $event->setException(new SymfonyAccessDenied($exception->getMessage(), $exception));
        }
    }
}