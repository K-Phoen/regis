<?php

declare(strict_types=1);

namespace Regis\Application\Command\Team;

use Regis\Domain\Entity;

class RemoveMember
{
    private $team;
    private $memberId;

    public function __construct(Entity\Team $team, string $memberId)
    {
        $this->team = $team;
        $this->memberId = $memberId;
    }

    public function getTeam(): Entity\Team
    {
        return $this->team;
    }

    public function getMemberId(): string
    {
        return $this->memberId;
    }
}