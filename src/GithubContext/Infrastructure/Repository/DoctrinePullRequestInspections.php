<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Regis\GithubContext\Infrastructure\Repository;

use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository;

class DoctrinePullRequestInspections implements Repository\PullRequestInspections
{
    use RepositoryHelper;

    public function save(Entity\PullRequestInspection $inspection): void
    {
        $this->entityManager()->persist($inspection);
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

    public function nextBuildNumber(Entity\Repository $repository): int
    {
        $qb = $this->entityManager()->createQueryBuilder();

        $qb
            ->select('MAX(inspection.number)')
            ->from(Entity\PullRequestInspection::class, 'inspection')
            ->where('inspection.repository = :repository')
            ->setParameter('repository', $repository);

        return $qb->getQuery()->getSingleScalarResult() + 1;
    }
}
