<?php

namespace Tests\Regis\GithubContext\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use RulerZ\RulerZ;
use RulerZ\Spec\Specification;

use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Infrastructure\Repository\DoctrineUsers;

class DoctrineUsersTest extends TestCase
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var EntityRepository */
    private $doctrineRepository;
    /** @var RulerZ */
    private $rulerz;
    /** @var DoctrineUsers */
    private $usersRepo;

    public function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->rulerz = $this->createMock(RulerZ::class);
        $this->doctrineRepository = $this->createMock(EntityRepository::class);

        $this->em
            ->method('getRepository')
            ->with(Entity\User::class)
            ->willReturn($this->doctrineRepository);

        $this->usersRepo = new DoctrineUsers($this->em, $this->rulerz);
    }

    public function testSaveUser()
    {
        $user = $this->createMock(Entity\User::class);

        $this->em->expects($this->once())
            ->method('persist')
            ->with($user);
        $this->em->expects($this->once())
            ->method('flush');

        $this->usersRepo->save($user);
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

        $this->assertSame($results, $this->usersRepo->matching($spec));
    }

    public function testFindByIdWhenTheUserExists()
    {
        $user = $this->createMock(Entity\User::class);

        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->willReturn($user);

        $this->assertSame($user, $this->usersRepo->findById('some identifier'));
    }

    /**
     * @expectedException \Regis\GithubContext\Domain\Repository\Exception\NotFound
     */
    public function testFindByIdWhenTheUserDoesNotExist()
    {
        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->willReturn(null);

        $this->usersRepo->findById('some identifier');
    }
}
