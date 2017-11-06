<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Command\Team;

use RulerZ\Spec\Specification;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\Spec\Team;
use Regis\GithubContext\Domain\Entity;

class RemoveMember implements Command\SecureCommandBySpecification
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

    public static function executionAuthorizedFor(Entity\User $user): Specification
    {
        return new Team\IsOwner($user);
    }

    public function getTargetToSecure()
    {
        return $this->team;
    }
}
