<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Bundle\AuthBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Regis\Application\Security\Context as SecurityContext;
use Regis\Domain\Entity;

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
