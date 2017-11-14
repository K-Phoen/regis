<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\CommandHandler\Repository;

use Regis\AppContext\Application\Command;
use Regis\AppContext\Domain\Repository;

class DisableInspections
{
    private $repositoriesRepo;

    public function __construct(Repository\Repositories $repositoriesRepo)
    {
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function handle(Command\Repository\DisableInspections $command)
    {
        $repo = $command->getRepository();
        $repo->disableInspection();

        $this->repositoriesRepo->save($repo);
    }
}
