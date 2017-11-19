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
use Regis\BitbucketContext\Domain\Entity;
use Regis\BitbucketContext\Infrastructure\Repository\DoctrineUsers;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrineUsersTest extends TestCase
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var RegistryInterface */
    private $registry;
    /** @var EntityRepository */
    private $doctrineRepository;
    /** @var DoctrineUsers */
    private $usersRepo;

    public function setUp()
    {
        $this->registry = $this->createMock(RegistryInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->doctrineRepository = $this->createMock(ObjectRepository::class);

        $this->registry->method('getManager')->willReturn($this->em);

        $this->em
            ->method('getRepository')
            ->with(Entity\BitbucketDetails::class)
            ->willReturn($this->doctrineRepository);

        $this->usersRepo = new DoctrineUsers($this->registry);
    }

    public function testSaveUser()
    {
        $user = $this->createMock(Entity\BitbucketDetails::class);

        $this->em->expects($this->once())->method('persist')->with($user);
        $this->em->expects($this->once())->method('flush');

        $this->usersRepo->save($user);
    }

    public function testFindByAccountIdWhenTheUserExists()
    {
        $user = $this->createMock(Entity\BitbucketDetails::class);

        $this->doctrineRepository
            ->method('findOneBy')
            ->with(['user' => 'some identifier'])
            ->willReturn($user);

        $this->assertSame($user, $this->usersRepo->findByAccountId('some identifier'));
    }

    /**
     * @expectedException \Regis\BitbucketContext\Domain\Repository\Exception\NotFound
     */
    public function testFindByAccountIdWhenTheUserDoesNotExist()
    {
        $this->doctrineRepository
            ->method('findOneBy')
            ->with(['user' => 'some identifier'])
            ->willReturn(null);

        $this->usersRepo->findByAccountId('some identifier');
    }

    public function testFindByBitbucketIdWhenTheUserExists()
    {
        $user = $this->createMock(Entity\BitbucketDetails::class);

        $this->doctrineRepository
            ->method('findOneBy')
            ->with(['remoteId' => 'some identifier'])
            ->willReturn($user);

        $this->assertSame($user, $this->usersRepo->findByBitbucketId('some identifier'));
    }

    /**
     * @expectedException \Regis\BitbucketContext\Domain\Repository\Exception\NotFound
     */
    public function testFindByBitbucketIdWhenTheUserDoesNotExist()
    {
        $this->doctrineRepository
            ->method('findOneBy')
            ->with(['remoteId' => 'some identifier'])
            ->willReturn(null);

        $this->usersRepo->findByBitbucketId('some identifier');
    }
}
