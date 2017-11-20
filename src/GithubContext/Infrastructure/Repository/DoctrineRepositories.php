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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository;

class DoctrineRepositories implements Repository\Repositories
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Entity\Repository $team): void
    {
        $this->em->persist($team);
        $this->em->flush();
    }

    public function find(string $id): Entity\Repository
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repo()->createQueryBuilder('r');

        $qb
            ->where('r.identifier = :identifier')
            ->setParameter('identifier', $id);

        $repository = $qb->getQuery()->getOneOrNullResult();

        if ($repository === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }

        return $repository;
    }

    private function repo(): EntityRepository
    {
        return $this->em->getRepository(Entity\Repository::class);
    }
}
