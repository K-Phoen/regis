<?php

declare(strict_types=1);

namespace Regis\Kernel\Bundle\AuthBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Regis\Kernel\Security\Context as SecurityContext;
use Regis\GithubContext\Domain\Entity;

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
