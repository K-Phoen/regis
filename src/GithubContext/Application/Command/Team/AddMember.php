<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Command\Team;

use RulerZ\Spec\Specification;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\Spec\Team;
use Regis\GithubContext\Domain\Entity;

class AddMember implements Command\SecureCommandBySpecification
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

    public static function executionAuthorizedFor(Entity\User $user): Specification
    {
        return new Team\IsOwner($user);
    }

    public function getTargetToSecure()
    {
        return $this->team;
    }
}
