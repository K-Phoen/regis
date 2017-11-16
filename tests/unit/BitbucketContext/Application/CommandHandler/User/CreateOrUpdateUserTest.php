<?php

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
        $command = new Command\User\CreateOrUpdateUser('user', 'remote-id', 'access token');

        $this->usersRepo
            ->method('findByBitbucketId')
            ->with('remote-id')
            ->willThrowException(new Repository\Exception\NotFound());

        $this->usersRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\BitbucketDetails $user) {
                $this->assertSame('remote-id', $user->getRemoteId());
                $this->assertSame('access token', $user->getAccessToken());

                return true;
            }));

        $this->handler->handle($command);
    }

    public function testItUpdatesTheUserIfItAlreadyExist()
    {
        $user = $this->createMock(Entity\BitbucketDetails::class);
        $command = new Command\User\CreateOrUpdateUser('user', 'remote-id', 'access token');

        $this->usersRepo
            ->method('findByBitbucketId')
            ->with('remote-id')
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
