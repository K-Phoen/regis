<?php

declare(strict_types=1);

namespace Regis\Application\Command\Team;

use RulerZ\Spec\Specification;

use Regis\Application\Command;
use Regis\Application\Spec\Team;
use Regis\Domain\Entity;

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