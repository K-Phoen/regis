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

namespace Tests\Regis\AppContext\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use RulerZ\RulerZ;
use RulerZ\Spec\Specification;
use Regis\AppContext\Domain\Entity;
use Regis\AppContext\Infrastructure\Repository\DoctrineTeams;

class DoctrineTeamsTest extends TestCase
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var EntityRepository */
    private $doctrineRepository;
    /** @var RulerZ */
    private $rulerz;
    /** @var DoctrineTeams */
    private $teamsRepo;

    public function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->rulerz = $this->createMock(RulerZ::class);
        $this->doctrineRepository = $this->createMock(EntityRepository::class);

        $this->em->expects($this->any())
            ->method('getRepository')
            ->with(Entity\Team::class)
            ->willReturn($this->doctrineRepository);

        $this->teamsRepo = new DoctrineTeams($this->em, $this->rulerz);
    }

    public function testMatching()
    {
        $qb = $this->createMock(QueryBuilder::class);
        $spec = $this->createMock(Specification::class);
        $results = new \ArrayIterator();

        $this->doctrineRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($qb);

        $this->rulerz->expects($this->once())
            ->method('filterSpec')
            ->with($qb, $spec)
            ->willReturn($results);

        $this->assertSame($results, $this->teamsRepo->matching($spec));
    }

    public function testSaveTeam()
    {
        $team = $this->createMock(Entity\Team::class);

        $this->em->expects($this->once())
            ->method('persist')
            ->with($team);
        $this->em->expects($this->once())
            ->method('flush');

        $this->teamsRepo->save($team);
    }

    /**
     * @expectedException \Regis\AppContext\Domain\Repository\Exception\UniqueConstraintViolation
     */
    public function testSaveTeamWrapsUniqueConstraintsViolations()
    {
        $team = $this->createMock(Entity\Team::class);
        $exception = $this->createMock(UniqueConstraintViolationException::class);

        $this->em->expects($this->once())
            ->method('flush')
            ->willThrowException($exception);

        $this->teamsRepo->save($team);
    }

    public function testFindWhenTheTeamExists()
    {
        $team = $this->createMock(Entity\Team::class);

        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->willReturn($team);

        $this->assertSame($team, $this->teamsRepo->find('some identifier'));
    }

    /**
     * @expectedException \Regis\AppContext\Domain\Repository\Exception\NotFound
     */
    public function testFindWhenTheTeamDoesNotExist()
    {
        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->willReturn(null);

        $this->teamsRepo->find('some identifier');
    }
}
