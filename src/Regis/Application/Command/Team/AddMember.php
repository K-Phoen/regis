<?php

declare(strict_types=1);

namespace Regis\Application\Command\Team;

use Regis\Domain\Entity;

class AddMember
{
    private $team;
    private $newMemberId;

    public function __construct(Entity\Team $team, string $newMemberId)
    {
        $this->team = $team;
        $this->newMemberId = $newMemberId;
    }

    public function getTeam(): Entity\Team
    {
        return $this->team;
    }

    public function getNewMemberId(): string
    {
        return $this->newMemberId;
    }
}