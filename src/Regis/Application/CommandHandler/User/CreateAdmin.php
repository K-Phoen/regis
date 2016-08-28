<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\User;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Regis\Application\Command;
use Regis\Domain\Entity\User;
use Regis\Domain\Repository;

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
        $user = User::createAdmin($command->getUsername(), $command->getPassword(), $command->getEmail());
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $command->getPassword());

        $user->changePassword($encodedPassword);

        $this->usersRepo->save($user);
    }
}