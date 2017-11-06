<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\CommandHandler\Repository;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Repository;

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
