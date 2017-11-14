<?php

declare(strict_types=1);

namespace Regis\AppContext\Infrastructure\Repository;

use Regis\AppContext\Domain\Entity;
use Regis\AppContext\Domain\Repository;

class DoctrineUsers implements Repository\Users
{
    use RepositoryHelper;

    public function findById(string $id): Entity\User
    {
        $user = $this->entityManager()->getRepository(Entity\User::class)->find($id);

        if ($user === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }

        return $user;
    }
}
