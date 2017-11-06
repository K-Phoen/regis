<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\CommandHandler\Repository;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Repository;

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
