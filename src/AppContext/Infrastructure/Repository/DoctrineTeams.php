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

namespace Regis\AppContext\Infrastructure\Repository;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use RulerZ\RulerZ;
use RulerZ\Spec\Specification;
use Regis\AppContext\Domain\Entity;
use Regis\AppContext\Domain\Repository;

class DoctrineTeams implements Repository\Teams
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var RulerZ */
    private $rulerz;

    public function __construct(EntityManagerInterface $em, RulerZ $rulerz)
    {
        $this->em = $em;
        $this->rulerz = $rulerz;
    }

    public function save(Entity\Team $team)
    {
        $this->em->persist($team);

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new Repository\Exception\UniqueConstraintViolation($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function matching(Specification $spec): \Traversable
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repo()->createQueryBuilder('t');

        return $this->rulerz->filterSpec($qb, $spec);
    }

    public function find(string $id): Entity\Team
    {
        $team = $this->repo()->find($id);

        if ($team === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }

        return $team;
    }

    private function repo()
    {
        return $this->em->getRepository(Entity\Team::class);
    }
}
