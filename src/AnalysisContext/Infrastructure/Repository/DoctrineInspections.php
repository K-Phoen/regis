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

namespace Regis\AnalysisContext\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Regis\AnalysisContext\Domain\Entity;
use Regis\AnalysisContext\Domain\Repository;

class DoctrineInspections implements Repository\Inspections
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Entity\Inspection $inspections): void
    {
        $this->em->persist($inspections);
        $this->em->flush();
    }

    public function find(string $id): Entity\Inspection
    {
        $inspection = $this->em->getRepository(Entity\Inspection::class)->find($id);

        if ($inspection === null) {
            throw Repository\Exception\NotFound::forIdentifier($id);
        }

        return $inspection;
    }
}
