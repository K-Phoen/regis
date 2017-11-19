<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\CommandHandler\Team;

use Regis\AppContext\Application\Command;
use Regis\AppContext\Domain\Repository;

class Leave
{
    private $teamsRepo;

    public function __construct(Repository\Teams $teamsRepo)
    {
        $this->teamsRepo = $teamsRepo;
    }

    public function handle(Command\Team\Leave $command)
    {
        $team = $command->getTeam();
        $team->removeMember($command->getUser());

        $this->teamsRepo->save($team);
    }
}
