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

namespace Regis\AppContext\Infrastructure\Bundle\AppBundle\Security;

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

    public function supportsClass($class): bool
    {
        return $class instanceof User;
    }
}
