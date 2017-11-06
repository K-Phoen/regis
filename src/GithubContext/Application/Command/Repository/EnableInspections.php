<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Command\Repository;

use RulerZ\Spec\Specification;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\Spec\Repository;
use Regis\GithubContext\Domain\Entity;

class EnableInspections implements Command\SecureCommandBySpecification
{
    private $repository;

    public function __construct(Entity\Repository $repository)
    {
        $this->repository = $repository;
    }

    public function getRepository(): Entity\Repository
    {
        return $this->repository;
    }

    public static function executionAuthorizedFor(Entity\User $user): Specification
    {
        return new Repository\IsOwner($user);
    }

    public function getTargetToSecure()
    {
        return $this->repository;
    }
}
