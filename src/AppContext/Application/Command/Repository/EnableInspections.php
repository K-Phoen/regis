<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\Command\Repository;

use RulerZ\Spec\Specification;

use Regis\AppContext\Application\Command;
use Regis\AppContext\Application\Spec\Repository;
use Regis\AppContext\Domain\Entity;

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
