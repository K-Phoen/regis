<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Team;

use Regis\Application\Command;
use Regis\Domain\Repository;

class AddRepository
{
    private $teamsRepo;
    private $repositoriesRepo;

    public function __construct(Repository\Teams $teamsRepo, Repository\Repositories $repositoriesRepo)
    {
        $this->teamsRepo = $teamsRepo;
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function handle(Command\Team\AddRepository $command)
    {
        $newRepo = $this->repositoriesRepo->find($command->getNewRepositoryId());

        $team = $command->getTeam();
        $team->addRepository($newRepo);

        try {
            $this->teamsRepo->save($team);
        } catch (Repository\Exception\UniqueConstraintViolation $e) {
            // The given repo was already in the team, nothing to do!
        }
    }
}