<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\CommandHandler\Team;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository;

class Create
{
    private $teamsRepo;

    public function __construct(Repository\Teams $teamsRepo)
    {
        $this->teamsRepo = $teamsRepo;
    }

    public function handle(Command\Team\Create $command)
    {
        $team = new Entity\Team($command->getOwner(), $command->getName());

        $this->teamsRepo->save($team);
    }
}
