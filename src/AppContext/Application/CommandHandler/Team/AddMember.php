<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\CommandHandler\Team;

use Regis\AppContext\Application\Command;
use Regis\AppContext\Domain\Repository;

class AddMember
{
    private $teamsRepo;
    private $usersRepo;

    public function __construct(Repository\Teams $teamsRepo, Repository\Users $usersRepo)
    {
        $this->teamsRepo = $teamsRepo;
        $this->usersRepo = $usersRepo;
    }

    public function handle(Command\Team\AddMember $command)
    {
        $newMember = $this->usersRepo->findById($command->getNewMemberId());

        $team = $command->getTeam();
        $team->addMember($newMember);

        try {
            $this->teamsRepo->save($team);
        } catch (Repository\Exception\UniqueConstraintViolation $e) {
            // The given user was already in the team, nothing to do!
        }
    }
}
