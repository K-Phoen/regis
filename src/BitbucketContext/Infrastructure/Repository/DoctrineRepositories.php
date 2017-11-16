<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Infrastructure\Repository;

use Regis\BitbucketContext\Domain\Entity;
use Regis\BitbucketContext\Domain\Repository;

class DoctrineRepositories implements Repository\Repositories
{
    use RepositoryHelper;

    public function find(string $id): Entity\Repository
    {
        $inspection = $this->entityManager()->getRepository(Entity\Repository::class)->findOneBy(['identifier' => $id]);

        if ($inspection === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }

        return $inspection;
    }
}
