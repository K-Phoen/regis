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

namespace Tests\Regis\BitbucketContext\Application\CommandHandler\User;

use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Application\CommandHandler;
use Regis\BitbucketContext\Domain\Entity;
use Regis\BitbucketContext\Domain\Repository;

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
        $accessTokenExpiration = new \DateTimeImmutable();
        $command = new Command\User\CreateOrUpdateUser('user', 'remote-id', 'access token', 'refresh token', $accessTokenExpiration);

        $this->usersRepo
            ->method('findByBitbucketId')
            ->with('remote-id')
            ->willThrowException(new Repository\Exception\NotFound());

        $this->usersRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\BitbucketDetails $user) use ($accessTokenExpiration) {
                $this->assertSame('remote-id', $user->getRemoteId());
                $this->assertSame('access token', $user->getAccessToken());
                $this->assertSame('refresh token', $user->getRefreshToken());
                $this->assertSame($accessTokenExpiration, $user->getAccessTokenExpiration());

                return true;
            }));

        $this->handler->handle($command);
    }

    public function testItUpdatesTheUserIfItAlreadyExist()
    {
        $user = $this->createMock(Entity\BitbucketDetails::class);
        $accessTokenExpiration = new \DateTimeImmutable();
        $command = new Command\User\CreateOrUpdateUser('user', 'remote-id', 'access token', 'refresh token', $accessTokenExpiration);

        $this->usersRepo
            ->method('findByBitbucketId')
            ->with('remote-id')
            ->willReturn($user);

        $this->usersRepo->expects($this->once())
            ->method('save')
            ->with($user);

        $user->expects($this->once())
            ->method('changeAccessToken')
            ->with('access token', $accessTokenExpiration, 'refresh token');

        $this->handler->handle($command);
    }
}
