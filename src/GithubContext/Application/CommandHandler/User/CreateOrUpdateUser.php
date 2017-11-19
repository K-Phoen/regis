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

namespace Regis\GithubContext\Application\CommandHandler\User;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Entity\GithubDetails;
use Regis\GithubContext\Domain\Entity\UserAccount;
use Regis\GithubContext\Domain\Repository;

class CreateOrUpdateUser
{
    private $usersRepo;

    public function __construct(Repository\Users $usersRepo)
    {
        $this->usersRepo = $usersRepo;
    }

    public function handle(Command\User\CreateOrUpdateUser $command)
    {
        try {
            $user = $this->usersRepo->findByGithubId($command->getGithubId());
            $user->changeAccessToken($command->getAccessToken());
        } catch (Repository\Exception\NotFound $e) {
            $user = new GithubDetails(new UserAccount(), $command->getUsername(), $command->getGithubId(), $command->getAccessToken());
        }

        $this->usersRepo->save($user);

        return $user;
    }
}
