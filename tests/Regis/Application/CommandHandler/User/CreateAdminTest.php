<?php

namespace Tests\Regis\Application\CommandHandler\User;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Domain\Entity;
use Regis\Domain\Repository;

class CreateAdminTest extends \PHPUnit_Framework_TestCase
{
    private $usersRepo;
    private $passwordEncoder;
    /** @var CommandHandler\User\CreateAdmin */
    private $handler;

    public function setUp()
    {
        $this->usersRepo = $this->getMockBuilder(Repository\Users::class)->getMock();
        $this->passwordEncoder = $this->getMockBuilder(UserPasswordEncoderInterface::class)->getMock();

        $this->handler = new CommandHandler\User\CreateAdmin($this->usersRepo, $this->passwordEncoder);
    }

    public function testItCreatesAnAdmin()
    {
        $command = new Command\User\CreateAdmin('admin', 'password', 'email');

        $this->passwordEncoder->expects($this->once())
            ->method('encodePassword')
            ->with($this->anything(), 'password')
            ->will($this->returnValue('encoded password'));

        $this->usersRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\User $user) {
                return in_array('ROLE_ADMIN', $user->getRoles())
                    && $user->getPassword() === 'encoded password'
                    && $user->getEmail() === 'email';
            }));

        $this->handler->handle($command);
    }
}
