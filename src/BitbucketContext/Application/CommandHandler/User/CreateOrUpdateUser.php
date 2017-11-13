<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\CommandHandler\User;

use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Domain\Entity\User;
use Regis\BitbucketContext\Domain\Repository;

class CreateOrUpdateUser
{
    private $usersRepo;

    public function __construct(Repository\Users $usersRepo)
    {
        $this->usersRepo = $usersRepo;
    }

    public function handle(Command\User\CreateOrUpdateUser $command)
    {
        try {
            $user = $this->usersRepo->findByBitbucketId($command->getBitbucketId());
            $user->changeAccessToken($command->getAccessToken());
        } catch (Repository\Exception\NotFound $e) {
            $user = User::createUser($command->getUsername(), $command->getBitbucketId(), $command->getAccessToken());
        }

        $this->usersRepo->save($user);

        return $user;
    }
}
