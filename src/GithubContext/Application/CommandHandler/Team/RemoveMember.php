<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\CommandHandler\Team;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Repository;

class RemoveMember
{
    private $teamsRepo;
    private $usersRepo;

    public function __construct(Repository\Teams $teamsRepo, Repository\Users $usersRepo)
    {
        $this->teamsRepo = $teamsRepo;
        $this->usersRepo = $usersRepo;
    }

    public function handle(Command\Team\RemoveMember $command)
    {
        $member = $this->usersRepo->findById($command->getMemberId());

        $team = $command->getTeam();
        $team->removeMember($member);

        $this->teamsRepo->save($team);
    }
}