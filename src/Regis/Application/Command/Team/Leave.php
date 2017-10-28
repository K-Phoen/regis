<?php

declare(strict_types=1);

namespace Regis\Application\Command\Team;

use RulerZ\Spec\Specification;

use Regis\Application\Command;
use Regis\Application\Spec\Team;
use Regis\Domain\Entity;

class Leave implements Command\SecureCommandBySpecification
{
    private $team;
    private $user;

    public function __construct(Entity\Team $team, Entity\User $user)
    {
        $this->team = $team;
        $this->user = $user;
    }

    public function getTeam(): Entity\Team
    {
        return $this->team;
    }

    public function getUser(): Entity\User
    {
        return $this->user;
    }

    public static function executionAuthorizedFor(Entity\User $user): Specification
    {
        return (new Team\IsOwner($user))->not();
    }

    public function getTargetToSecure()
    {
        return $this->team;
    }
}
