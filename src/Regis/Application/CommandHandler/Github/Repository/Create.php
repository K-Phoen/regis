<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Github\Repository;

use Regis\Application\Command;
use Regis\Domain\Entity;
use Regis\Domain\Repository\Repositories;

class Create
{
    private $repositoriesRepo;

    public function __construct(Repositories $repositoriesRepo)
    {
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function handle(Command\Github\Repository\Create $command)
    {
        $this->repositoriesRepo->save(new Entity\Github\Repository(
            $command->getOwner(),
            $command->getIdentifier(),
            $command->getSharedSecret()
        ));
    }
}
