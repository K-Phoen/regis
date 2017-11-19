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

use Doctrine\Common\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Regis\BitbucketContext\Infrastructure\Repository\DoctrineRepositories;
use Regis\BitbucketContext\Domain\Entity;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrineRepositoriesTest extends TestCase
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var RegistryInterface */
    private $registry;
    /** @var EntityRepository */
    private $doctrineRepository;
    /** @var DoctrineRepositories */
    private $repositoriesRepo;

    public function setUp()
    {
        $this->registry = $this->createMock(RegistryInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->doctrineRepository = $this->createMock(ObjectRepository::class);

        $this->registry->method('getManager')->willReturn($this->em);

        $this->em
            ->method('getRepository')
            ->with(Entity\Repository::class)
            ->willReturn($this->doctrineRepository);

        $this->repositoriesRepo = new DoctrineRepositories($this->registry);
    }

    public function testFindWhenTheRepositoryExists()
    {
        $repository = $this->createMock(Entity\Repository::class);

        $this->doctrineRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['identifier' => 'some identifier'])
            ->willReturn($repository);

        $this->assertSame($repository, $this->repositoriesRepo->find('some identifier'));
    }

    /**
     * @expectedException \Regis\BitbucketContext\Domain\Repository\Exception\NotFound
     */
    public function testFindWhenTheRepositoryDoesNotExist()
    {
        $this->doctrineRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['identifier' => 'some identifier'])
            ->willReturn(null);

        $this->repositoriesRepo->find('some identifier');
    }
}
