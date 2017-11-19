<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\CommandHandler\Repository;

use Regis\AppContext\Application\Command;
use Regis\GithubContext\Application\Random\Generator as RandomGenerator;
use Regis\AppContext\Domain\Entity;
use Regis\AppContext\Domain\Repository\Repositories;
use Regis\AppContext\Domain\Repository\Exception;

class Register
{
    private $repositoriesRepo;
    private $randomGenerator;

    public function __construct(Repositories $repositoriesRepo, RandomGenerator $generator)
    {
        $this->repositoriesRepo = $repositoriesRepo;
        $this->randomGenerator = $generator;
    }

    public function handle(Command\Repository\Register $command)
    {
        try {
            return $this->repositoriesRepo->find($command->getIdentifier());
        } catch (Exception\NotFound $e) {
            // the repository does not exist yet, we can continue and create it.
        }

        $sharedSecret = $command->getSharedSecret() ?: $this->randomGenerator->randomString();
        $repository = new Entity\Repository(
            $command->getOwner(),
            $command->getType(),
            $command->getIdentifier(),
            $command->getName(),
            $sharedSecret
        );

        $this->repositoriesRepo->save($repository);

        return $repository;
    }
}
