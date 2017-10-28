<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Team;

use Regis\Application\Command;
use Regis\Domain\Entity;
use Regis\Domain\Repository;

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
