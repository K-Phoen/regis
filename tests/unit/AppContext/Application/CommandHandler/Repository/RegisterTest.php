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

namespace Tests\Regis\AppContext\Application\CommandHandler\Repository;

use PHPUnit\Framework\TestCase;
use Regis\AppContext\Application\Command;
use Regis\AppContext\Application\CommandHandler;
use Regis\GithubContext\Application\Random;
use Regis\AppContext\Domain\Entity;
use Regis\AppContext\Domain\Repository\Exception;
use Regis\AppContext\Domain\Repository\Repositories;
use Regis\Kernel;

class RegisterTest extends TestCase
{
    private $repositoriesRepo;
    private $randomGenerator;
    /** @var CommandHandler\Repository\Register */
    private $handler;

    public function setUp()
    {
        $this->repositoriesRepo = $this->createMock(Repositories::class);
        $this->randomGenerator = $this->createMock(Random\Generator::class);

        $this->handler = new CommandHandler\Repository\Register($this->repositoriesRepo, $this->randomGenerator);
    }

    public function testItBuildsAndSaveTheEntityIfItDoesNotAlreadyExists()
    {
        $user = $this->createMock(Kernel\User::class);

        $this->repositoriesRepo
            ->method('findByIdentifier')
            ->with(Entity\Repository::TYPE_GITHUB, 'some identifier')
            ->willThrowException(new Exception\NotFound());

        $this->repositoriesRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\Repository $repository) use ($user) {
                $this->assertSame($user, $repository->getOwner());
                $this->assertSame('some identifier', $repository->getIdentifier());
                $this->assertSame('shared secret', $repository->getSharedSecret());

                return true;
            }));

        $command = new Command\Repository\Register($user, Entity\Repository::TYPE_GITHUB, 'some identifier', 'some name', 'shared secret');
        $repository = $this->handler->handle($command);

        $this->assertInstanceOf(Entity\Repository::class, $repository);
    }

    public function testItReturnsTheRepositoryIfItAlreadyExists()
    {
        $user = $this->createMock(Kernel\User::class);
        $repository = $this->createMock(Entity\Repository::class);

        $this->repositoriesRepo->expects($this->once())
            ->method('findByIdentifier')
            ->with(Entity\Repository::TYPE_GITHUB, 'some identifier')
            ->willReturn($repository);

        $command = new Command\Repository\Register($user, Entity\Repository::TYPE_GITHUB, 'some identifier', 'some name', 'shared secret');
        $this->assertSame($repository, $this->handler->handle($command));
    }

    public function testItGeneratesASecretIfNoneIsGiven()
    {
        $user = $this->createMock(Kernel\User::class);
        $generatedSecret = 'totally random string, trust me';

        $this->repositoriesRepo
            ->method('findByIdentifier')
            ->with(Entity\Repository::TYPE_GITHUB, 'some identifier')
            ->willThrowException(new Exception\NotFound());

        $this->randomGenerator->method('randomString')->willReturn($generatedSecret);

        $this->repositoriesRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\Repository $repository) use ($generatedSecret) {
                $this->assertSame($generatedSecret, $repository->getSharedSecret());

                return true;
            }));

        $command = new Command\Repository\Register($user, Entity\Repository::TYPE_GITHUB, 'some identifier', 'some name');
        $this->handler->handle($command);
    }
}
