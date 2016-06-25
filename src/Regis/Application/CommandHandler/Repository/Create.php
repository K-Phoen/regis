<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Repository;

use Regis\Application\Command;
use Regis\Application\Entity;
use Regis\Application\Repository\Repositories;

class Create
{
    private $repositoriesRepo;

    public function __construct(Repositories $repositoriesRepo)
    {
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function handle(Command\Repository\Create $command)
    {
        $this->repositoriesRepo->save(new Entity\Repository(
            $command->getIdentifier(),
            $command->getSharedSecret()
        ));
    }
}
