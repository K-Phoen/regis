<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\Command\Team;

use RulerZ\Spec\Specification;
use Regis\AppContext\Application\Command;
use Regis\AppContext\Application\Spec\Team;
use Regis\AppContext\Domain\Entity;

class AddRepository implements Command\SecureCommandBySpecification
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

    public static function executionAuthorizedFor(Entity\User $user): Specification
    {
        return new Team\IsOwner($user);
    }

    public function getTargetToSecure()
    {
        return $this->team;
    }
}
