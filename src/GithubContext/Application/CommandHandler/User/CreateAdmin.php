<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\CommandHandler\User;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Entity\User;
use Regis\GithubContext\Domain\Repository;

/**
 * TODO we should not directly rely on Symfony's password encoder interface.
 */
class CreateAdmin
{
    private $usersRepo;
    private $passwordEncoder;

    public function __construct(Repository\Users $usersRepo, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->usersRepo = $usersRepo;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function handle(Command\User\CreateAdmin $command)
    {
        $user = User::createAdmin($command->getUsername(), $command->getPassword());
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $command->getPassword());

        $user->changePassword($encodedPassword);

        $this->usersRepo->save($user);
    }
}
