<?php

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
