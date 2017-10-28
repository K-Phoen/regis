<?php

declare(strict_types=1);

namespace Regis\Application\Command\Team;

use RulerZ\Spec\Specification;

use Regis\Application\Command;
use Regis\Application\Spec\Team;
use Regis\Domain\Entity;

class RemoveRepository implements Command\SecureCommandBySpecification
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

    public static function executionAuthorizedFor(Entity\User $user): Specification
    {
        return new Team\IsOwner($user);
    }

    public function getTargetToSecure()
    {
        return $this->team;
    }
}
