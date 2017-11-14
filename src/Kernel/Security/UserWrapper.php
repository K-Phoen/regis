<?php

declare(strict_types=1);

namespace Regis\Kernel\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Regis\Kernel;

class UserWrapper implements UserInterface, Kernel\User
{
    private $kernelUser;

    public function __construct(Kernel\User $kernelUser)
    {
        $this->kernelUser = $kernelUser;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->kernelUser->accountId();
    }

    /**
     * {@inheritdoc}
     */
    public function accountId(): string
    {
        return $this->kernelUser->accountId();
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }
}
