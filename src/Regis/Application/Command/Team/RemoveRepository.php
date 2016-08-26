<?php

declare(strict_types=1);

namespace Regis\Application\Command\Team;

use Regis\Domain\Entity;

class RemoveRepository
{
    private $team;
    private $repositoryId;

    public function __construct(Entity\Team $team, string $repositoryId)
    {
        $this->team = $team;
        $this->repositoryId = $repositoryId;
    }

    public function getTeam(): Entity\Team
    {
        return $this->team;
    }

    public function getRepositoryId(): string
    {
        return $this->repositoryId;
    }
}