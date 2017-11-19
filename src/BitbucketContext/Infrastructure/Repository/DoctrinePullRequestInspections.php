<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Infrastructure\Repository;

use Regis\BitbucketContext\Domain\Entity;
use Regis\BitbucketContext\Domain\Repository;

class DoctrinePullRequestInspections implements Repository\PullRequestInspections
{
    use RepositoryHelper;

    public function save(Entity\PullRequestInspection $inspections)
    {
        $this->entityManager()->persist($inspections);
        $this->entityManager()->flush();
    }

    public function find(string $id): Entity\PullRequestInspection
    {
        // FIXME without this, the workers will always return the entities stored in memory
        $this->entityManager()->close();

        $inspection = $this->entityManager()->getRepository(Entity\PullRequestInspection::class)->find($id);

        if ($inspection === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }

        return $inspection;
    }
}
