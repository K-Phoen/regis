<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\CommandHandler\Repository;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\Random\Generator as RandomGenerator;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository\Repositories;
use Regis\GithubContext\Domain\Repository\Exception;

class RegisterRepository
{
    private $repositoriesRepo;
    private $randomGenerator;

    public function __construct(Repositories $repositoriesRepo, RandomGenerator $generator)
    {
        $this->repositoriesRepo = $repositoriesRepo;
        $this->randomGenerator = $generator;
    }

    public function handle(Command\Repository\RegisterRepository $command)
    {
        try {
            return $this->repositoriesRepo->find($command->getIdentifier());
        } catch (Exception\NotFound $e) {
            // the repository does not exist yet, we can continue and create it.
        }

        $sharedSecret = $command->getSharedSecret() ?: $this->randomGenerator->randomString();
        $repository = new Entity\Repository($command->getOwner(), $command->getIdentifier(), $sharedSecret);

        $this->repositoriesRepo->save($repository);

        return $repository;
    }
}
