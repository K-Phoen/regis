<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\Command\Team;

use RulerZ\Spec\Specification;

use Regis\AppContext\Application\Command;
use Regis\AppContext\Application\Spec\Team;
use Regis\AppContext\Domain\Entity;

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
