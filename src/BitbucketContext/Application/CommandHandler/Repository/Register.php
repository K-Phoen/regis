<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\CommandHandler\Repository;

use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Domain\Entity;
use Regis\BitbucketContext\Domain\Repository\Repositories;
use Regis\BitbucketContext\Domain\Repository\Exception;

class Register
{
    private $repositoriesRepo;

    public function __construct(Repositories $repositoriesRepo)
    {
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function handle(Command\Repository\Register $command)
    {
        try {
            return $this->repositoriesRepo->find($command->getIdentifier());
        } catch (Exception\NotFound $e) {
            // the repository does not exist yet, we can continue and create it.
        }

        $repository = new Entity\Repository($command->getOwner(), $command->getIdentifier());

        $this->repositoriesRepo->save($repository);

        return $repository;
    }
}
