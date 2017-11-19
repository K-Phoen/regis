<?php

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
