<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Team;

use Regis\Application\Command;
use Regis\Domain\Repository;

class RemoveRepository
{
    private $teamsRepo;
    private $repositoriesRepo;

    public function __construct(Repository\Teams $teamsRepo, Repository\Repositories $repositoriesRepo)
    {
        $this->teamsRepo = $teamsRepo;
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function handle(Command\Team\RemoveRepository $command)
    {
        $repo = $this->repositoriesRepo->find($command->getRepositoryId());

        $team = $command->getTeam();
        $team->removeRepository($repo);

        $this->teamsRepo->save($team);
    }
}
