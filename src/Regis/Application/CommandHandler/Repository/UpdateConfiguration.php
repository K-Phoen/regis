<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Repository;

use Regis\Application\Command;
use Regis\Application\Entity;
use Regis\Application\Repository\Repositories;

class UpdateConfiguration
{
    private $repositoriesRepo;

    public function __construct(Repositories $repositoriesRepo)
    {
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function handle(Command\Repository\UpdateConfiguration $command)
    {
        $command->getRepository()->newSharedSecret($command->getNewSharedSecret());

        $this->repositoriesRepo->save($command->getRepository());
    }
}
