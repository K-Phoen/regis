<?php

namespace Tests\Regis\Application\CommandHandler\User;

use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Domain\Entity;
use Regis\Domain\Repository;

class CreateOrUpdateUserTest extends \PHPUnit_Framework_TestCase
{
    private $usersRepo;
    /** @var CommandHandler\User\CreateOrUpdateUser */
    private $handler;

    public function setUp()
    {
        $this->usersRepo = $this->getMockBuilder(Repository\Users::class)->getMock();

        $this->handler = new CommandHandler\User\CreateOrUpdateUser($this->usersRepo);
    }

    public function testItCreatesANewUserIfItDoesNotAlreadyExist()
    {
        $command = new Command\User\CreateOrUpdateUser('user', 42, 'email', 'access token');

        $this->usersRepo->expects($this->once())
            ->method('findByGithubId')
            ->with(42)
            ->will($this->throwException(new Repository\Exception\NotFound));

        $this->usersRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function(Entity\User $user) {
                return in_array('ROLE_USER', $user->getRoles(), true)
                && $user->getUsername() === 'user'
                && $user->getEmail() === 'email'
                && $user->getGithubId() === 42
                && $user->getGithubAccessToken() === 'access token';
            }));

        $this->handler->handle($command);
    }

    public function testItUpdatesTheUserIfItAlreadyExist()
    {
        $user = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $command = new Command\User\CreateOrUpdateUser('user', 42, 'email', 'access token');

        $this->usersRepo->expects($this->once())
            ->method('findByGithubId')
            ->with(42)
            ->will($this->returnValue($user));

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
