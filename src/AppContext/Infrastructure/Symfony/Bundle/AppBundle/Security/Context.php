<?php

declare(strict_types=1);

namespace Regis\AppContext\Infrastructure\Symfony\Bundle\AppBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Regis\Kernel\Security\Context as SecurityContext;
use Regis\AppContext\Domain\Entity;

class Context implements SecurityContext
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getUser(): Entity\User
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}
