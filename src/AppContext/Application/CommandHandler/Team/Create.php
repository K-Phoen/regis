<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\CommandHandler\Team;

use Regis\AppContext\Application\Command;
use Regis\AppContext\Domain\Entity;
use Regis\AppContext\Domain\Repository;

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
