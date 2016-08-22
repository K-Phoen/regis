<?php

declare(strict_types=1);

namespace Regis\Application\Command\Team;

use Regis\Domain\Entity;

class AddRepository
{
    private $team;
    private $newRepositoryId;

    public function __construct(Entity\Team $team, string $newRepositoryId)
    {
        $this->team = $team;
        $this->newRepositoryId = $newRepositoryId;
    }

    public function getTeam(): Entity\Team
    {
        return $this->team;
    }

    public function getNewRepositoryId(): string
    {
        return $this->newRepositoryId;
    }
}