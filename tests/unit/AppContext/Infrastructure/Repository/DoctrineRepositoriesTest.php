<?php

namespace Tests\Regis\AppContext\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Regis\AppContext\Domain\Repository\Repositories;
use RulerZ\RulerZ;

use Regis\AppContext\Infrastructure\Repository\DoctrineRepositories;
use Regis\AppContext\Domain\Entity;
use RulerZ\Spec\Specification;

class DoctrineRepositoriesTest extends TestCase
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var EntityRepository */
    private $doctrineRepository;
    /** @var DoctrineRepositories */
    private $repositoriesRepo;
    /** @var RulerZ */
    private $rulerz;

    public function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->doctrineRepository = $this->createMock(EntityRepository::class);
        $this->rulerz = $this->createMock(RulerZ::class);

        $this->em
            ->method('getRepository')
            ->with(Entity\Repository::class)
            ->willReturn($this->doctrineRepository);

        $this->repositoriesRepo = new DoctrineRepositories($this->em, $this->rulerz);
    }

    public function testSaveRepository()
    {
        $repository = $this->createMock(Entity\Repository::class);

        $this->em->expects($this->once())
            ->method('persist')
            ->with($repository);
        $this->em->expects($this->once())
            ->method('flush');

        $this->repositoriesRepo->save($repository);
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

        $this->assertSame($results, $this->repositoriesRepo->matching($spec));
    }

    public function testFindWhenTheRepositoryExists()
    {
        $repository = $this->createMock(Entity\Repository::class);
        $qb = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);

        $qb->expects($this->once())
            ->method('where')
            ->willReturnSelf();

        $qb->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $query->expects($this->once())
            ->method('getOneOrNullResult')
            ->willReturn($repository);

        $this->doctrineRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($qb);

        $this->assertSame($repository, $this->repositoriesRepo->find('some identifier'));
    }

    public function testFindWithRelations()
    {
        $repository = $this->createMock(Entity\Repository::class);
        $qb = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);

        $qb->expects($this->once())
            ->method('where')
            ->willReturnSelf();

        $qb->expects($this->once())
            ->method('addSelect') // the relations
            ->willReturnSelf();

        $qb
            ->method('leftJoin') // same: the relations
            ->willReturnSelf();

        $qb->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $query->expects($this->once())
            ->method('getOneOrNullResult')
            ->willReturn($repository);

        $this->doctrineRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($qb);

        $this->assertSame($repository, $this->repositoriesRepo->find('some identifier', Repositories::MODE_FETCH_RELATIONS));
    }

    /**
     * @expectedException \Regis\AppContext\Domain\Repository\Exception\NotFound
     */
    public function testFindWhenTheRepositoryDoesNotExist()
    {
        $qb = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);

        $qb->expects($this->once())
            ->method('where')
            ->willReturnSelf();

        $qb->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $query->expects($this->once())
            ->method('getOneOrNullResult')
            ->willReturn(null);

        $this->doctrineRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($qb);

        $this->repositoriesRepo->find('some identifier');
    }
}
