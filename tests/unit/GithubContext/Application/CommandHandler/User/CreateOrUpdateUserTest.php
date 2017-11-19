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

namespace Tests\Regis\GithubContext\Application\CommandHandler\User;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\CommandHandler;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository;

class CreateOrUpdateUserTest extends TestCase
{
    private $usersRepo;

    /** @var CommandHandler\User\CreateOrUpdateUser */
    private $handler;

    public function setUp()
    {
        $this->usersRepo = $this->createMock(Repository\Users::class);

        $this->handler = new CommandHandler\User\CreateOrUpdateUser($this->usersRepo);
    }

    public function testItCreatesANewUserIfItDoesNotAlreadyExist()
    {
        $command = new Command\User\CreateOrUpdateUser('user', 42, 'access token');

        $this->usersRepo
            ->method('findByGithubId')
            ->with(42)
            ->willThrowException(new Repository\Exception\NotFound());

        $this->usersRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\GithubDetails $user) {
                $this->assertSame(42, $user->getRemoteId());
                $this->assertSame('access token', $user->getAccessToken());
                $this->assertSame('user', $user->getUsername());

                return true;
            }));

        $this->handler->handle($command);
    }

    public function testItUpdatesTheUserIfItAlreadyExist()
    {
        $user = $this->createMock(Entity\GithubDetails::class);
        $command = new Command\User\CreateOrUpdateUser('user', 42, 'access token');

        $this->usersRepo
            ->method('findByGithubId')
            ->with(42)
            ->willReturn($user);

        $this->usersRepo->expects($this->once())
            ->method('save')
            ->with($user);

        $user->expects($this->once())
            ->method('changeAccessToken')
            ->with('access token');

        $this->handler->handle($command);
    }
}
