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

namespace Tests\Regis\BitbucketContext\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Regis\BitbucketContext\Infrastructure\Repository\DoctrinePullRequestInspections;
use Regis\BitbucketContext\Domain\Entity;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrinePullRequestInspectionsTest extends TestCase
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var RegistryInterface */
    private $registry;
    /** @var ObjectRepository */
    private $doctrineRepository;
    /** @var DoctrinePullRequestInspections */
    private $inspectionsRepo;

    public function setUp()
    {
        $this->registry = $this->createMock(RegistryInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->doctrineRepository = $this->createMock(ObjectRepository::class);

        $this->registry->method('getManager')->willReturn($this->em);

        $this->em
            ->method('getRepository')
            ->with(Entity\PullRequestInspection::class)
            ->willReturn($this->doctrineRepository);

        $this->inspectionsRepo = new DoctrinePullRequestInspections($this->registry);
    }

    public function testSaveInspection()
    {
        $inspection = $this->createMock(Entity\PullRequestInspection::class);

        $this->em->expects($this->once())
            ->method('persist')
            ->with($inspection);
        $this->em->expects($this->once())
            ->method('flush');

        $this->inspectionsRepo->save($inspection);
    }

    public function testFindWhenTheInspectionExists()
    {
        $inspection = $this->createMock(Entity\PullRequestInspection::class);

        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->willReturn($inspection);

        $this->assertSame($inspection, $this->inspectionsRepo->find('some identifier'));
    }

    /**
     * @expectedException \Regis\BitbucketContext\Domain\Repository\Exception\NotFound
     */
    public function testFindWhenTheInspectionDoesNotExist()
    {
        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->willReturn(null);

        $this->inspectionsRepo->find('some identifier');
    }
}
