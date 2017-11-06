<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Command\Team;

use RulerZ\Spec\Specification;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\Spec\Team;
use Regis\GithubContext\Domain\Entity;

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
