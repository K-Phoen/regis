<?php

namespace Tests\Regis\GithubContext\Application\CommandHandler\Repository;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\CommandHandler;
use Regis\GithubContext\Application\Random;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository\Exception;
use Regis\GithubContext\Domain\Repository\Repositories;

class RegisterRepositoryTest extends TestCase
{
    private $repositoriesRepo;
    private $randomGenerator;
    /** @var CommandHandler\Repository\DefineSharedSecret */
    private $handler;

    public function setUp()
    {
        $this->repositoriesRepo = $this->createMock(Repositories::class);
        $this->randomGenerator = $this->createMock(Random\Generator::class);

        $this->handler = new CommandHandler\Repository\RegisterRepository($this->repositoriesRepo, $this->randomGenerator);
    }

    public function testItBuildsAndSaveTheEntityIfItDoesNotAlreadyExists()
    {
        $user = $this->createMock(Entity\User::class);

        $this->repositoriesRepo
            ->method('find')
            ->with('some identifier')
            ->willThrowException(new Exception\NotFound());

        $this->repositoriesRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\Repository $repository) use ($user) {
                $this->assertSame($user, $repository->getOwner());
                $this->assertSame('some identifier', $repository->getIdentifier());
                $this->assertSame('shared secret', $repository->getSharedSecret());

                return true;
            }));

        $command = new Command\Repository\RegisterRepository($user, 'some identifier', 'shared secret');
        $repository = $this->handler->handle($command);

        $this->assertInstanceOf(Entity\Repository::class, $repository);
    }

    public function testItReturnsTheRepositoryIfItAlreadyExists()
    {
        $user = $this->createMock(Entity\User::class);
        $repository = $this->createMock(Entity\Repository::class);

        $this->repositoriesRepo->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->willReturn($repository);

        $command = new Command\Repository\RegisterRepository($user, 'some identifier', 'shared secret');
        $this->assertSame($repository, $this->handler->handle($command));
    }
}
