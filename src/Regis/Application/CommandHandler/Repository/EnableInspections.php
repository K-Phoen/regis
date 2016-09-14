<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Repository;

use Regis\Application\Command;
use Regis\Domain\Repository;

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
