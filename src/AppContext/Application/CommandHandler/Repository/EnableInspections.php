<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\CommandHandler\Repository;

use Regis\AppContext\Application\Command;
use Regis\AppContext\Domain\Repository;

class EnableInspections
{
    private $repositoriesRepo;

    public function __construct(Repository\Repositories $repositoriesRepo)
    {
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function handle(Command\Repository\EnableInspections $command)
    {
        $repo = $command->getRepository();
        $repo->enableInspection();

        $this->repositoriesRepo->save($repo);
    }
}
