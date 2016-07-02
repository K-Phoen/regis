<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Github\Repository;

use Regis\Application\Command;
use Regis\Application\Repository\Repositories;
use Regis\Domain\Entity;

class UpdateConfiguration
{
    private $repositoriesRepo;

    public function __construct(Repositories $repositoriesRepo)
    {
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function handle(Command\Github\Repository\UpdateConfiguration $command)
    {
        $command->getRepository()->newSharedSecret($command->getNewSharedSecret());

        $this->repositoriesRepo->save($command->getRepository());
    }
}
