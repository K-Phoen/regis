<?php

namespace Tests\Regis\GithubContext\Application\CommandHandler\User;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\CommandHandler;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository;

class CreateAdminTest extends TestCase
{
    private $usersRepo;
    private $passwordEncoder;
    /** @var CommandHandler\User\CreateAdmin */
    private $handler;

    public function setUp()
    {
        $this->usersRepo = $this->createMock(Repository\Users::class);
        $this->passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);

        $this->handler = new CommandHandler\User\CreateAdmin($this->usersRepo, $this->passwordEncoder);
    }

    public function testItCreatesAnAdmin()
    {
        $command = new Command\User\CreateAdmin('admin', 'password', 'email');

        $this->passwordEncoder->expects($this->once())
            ->method('encodePassword')
            ->with($this->anything(), 'password')
            ->willReturn('encoded password');

        $this->usersRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\User $user) {
                return in_array('ROLE_ADMIN', $user->getRoles())
                    && $user->getPassword() === 'encoded password';
            }));

        $this->handler->handle($command);
    }
}
