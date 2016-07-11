<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Github\Repository;

use Regis\Application\Command;
use Regis\Application\Random\Generator as RandomGenerator;
use Regis\Domain\Entity;
use Regis\Domain\Repository\Repositories;

class Create
{
    private $repositoriesRepo;
    private $randomGenerator;

    public function __construct(Repositories $repositoriesRepo, RandomGenerator $generator)
    {
        $this->repositoriesRepo = $repositoriesRepo;
        $this->randomGenerator = $generator;
    }

    public function handle(Command\Github\Repository\Create $command)
    {
        $sharedSecret = $command->getSharedSecret() ?: $this->randomGenerator->randomString();
        $repository = new Entity\Github\Repository($command->getOwner(), $command->getIdentifier(), $sharedSecret);

        $this->repositoriesRepo->save($repository);

        return $repository;
    }
}
