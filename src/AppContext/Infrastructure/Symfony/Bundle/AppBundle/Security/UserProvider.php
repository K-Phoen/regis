<?php

declare(strict_types=1);

namespace Regis\AppContext\Infrastructure\Symfony\Bundle\AppBundle\Security;

use Regis\AppContext\Domain\Entity\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Regis\AppContext\Domain\Repository;

class UserProvider implements UserProviderInterface
{
    private $usersRepo;

    public function __construct(Repository\Users $usersRepo)
    {
        $this->usersRepo = $usersRepo;
    }

    public function loadUserByUsername($username)
    {
        try {
            return $this->usersRepo->findById($username);
        } catch (Repository\Exception\NotFound $e) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username), $e->getCode(), $e);
        }
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->accountId());
    }

    public function supportsClass($class)
    {
        return $class instanceof User;
    }
}
