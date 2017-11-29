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

use Regis\AppContext\Domain\Entity;
use Regis\AppContext\Domain\Repository;
use RulerZ\RulerZ;
use RulerZ\Spec\Specification;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrineUsers implements Repository\Users
{
    use RepositoryHelper;

    /** @var RulerZ */
    private $rulerz;

    public function __construct(RegistryInterface $emRegistry, RulerZ $rulerz)
    {
        $this->emRegistry = $emRegistry;
        $this->rulerz = $rulerz;
    }

    public function matching(Specification $spec): \Traversable
    {
        /** @var \Doctrine\ORM\EntityRepository $repo */
        $repo = $this->entityManager()->getRepository(Entity\User::class);
        $qb = $repo->createQueryBuilder('u');

        return $this->rulerz->filterSpec($qb, $spec);
    }

    public function findById(string $id): Entity\User
    {
        $user = $this->entityManager()->getRepository(Entity\User::class)->find($id);

        if ($user === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }

        return $user;
    }
}
