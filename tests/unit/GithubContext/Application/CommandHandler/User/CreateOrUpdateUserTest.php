<?php

namespace Tests\Regis\GithubContext\Application\CommandHandler\Repository;

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
        $command = new Command\User\CreateOrUpdateUser('user', 42, 'access token', 'email');

        $this->usersRepo
            ->method('findByGithubId')
            ->with(42)
            ->willThrowException(new Repository\Exception\NotFound());

        $this->usersRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\User $user) {
                $this->assertEquals(['ROLE_USER'], $user->getRoles());
                $this->assertSame('user', $user->getUsername());
                $this->assertSame('email', $user->getEmail());
                $this->assertSame(42, $user->getGithubId());
                $this->assertSame('access token', $user->getGithubAccessToken());

                return true;
            }));

        $this->handler->handle($command);
    }

    public function testItCreatesANewUserEvenIfTheEmailIsNotSpecified()
    {
        $command = new Command\User\CreateOrUpdateUser('user', 42, 'access token');

        $this->usersRepo
            ->method('findByGithubId')
            ->with(42)
            ->willThrowException(new Repository\Exception\NotFound());

        $this->usersRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\User $user) {$this->assertEquals(['ROLE_USER'], $user->getRoles());
                $this->assertSame('user', $user->getUsername());
                $this->assertNull($user->getEmail());
                $this->assertSame(42, $user->getGithubId());
                $this->assertSame('access token', $user->getGithubAccessToken());

                return true;
            }));

        $this->handler->handle($command);
    }

    public function testItUpdatesTheUserIfItAlreadyExist()
    {
        $user = $this->createMock(Entity\User::class);
        $command = new Command\User\CreateOrUpdateUser('user', 42, 'access token', 'email');

        $this->usersRepo
            ->method('findByGithubId')
            ->with(42)
            ->willReturn($user);

        $this->usersRepo->expects($this->once())
            ->method('save')
            ->with($user);

        $user->expects($this->once())
            ->method('changeEmail')
            ->with('email');
        $user->expects($this->once())
            ->method('changeGithubAccessToken')
            ->with('access token');

        $this->handler->handle($command);
    }
}
