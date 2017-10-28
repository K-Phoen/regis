<?php

namespace Tests\Regis\Infrastructure\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Regis\Domain\Repository\Repositories;
use RulerZ\RulerZ;

use Regis\Infrastructure\Repository\DoctrineRepositories;
use Regis\Domain\Entity;
use RulerZ\Spec\Specification;

class DoctrineRepositoriesTest extends \PHPUnit_Framework_TestCase
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
        $this->em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $this->doctrineRepository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $this->rulerz = $this->getMockBuilder(RulerZ::class)->disableOriginalConstructor()->getMock();

        $this->em->expects($this->any())
            ->method('getRepository')
            ->with(Entity\Repository::class)
            ->will($this->returnValue($this->doctrineRepository));

        $this->repositoriesRepo = new DoctrineRepositories($this->em, $this->rulerz);
    }

    public function testSaveRepository()
    {
        $repository = $this->getMockBuilder(Entity\Repository::class)->disableOriginalConstructor()->getMock();

        $this->em->expects($this->once())
            ->method('persist')
            ->with($repository);
        $this->em->expects($this->once())
            ->method('flush');

        $this->repositoriesRepo->save($repository);
    }

    public function testMatching()
    {
        $qb = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $spec = $this->getMockBuilder(Specification::class)->getMock();
        $results = new \ArrayIterator();

        $this->doctrineRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($qb));

        $this->rulerz->expects($this->once())
            ->method('filterSpec')
            ->with($qb, $spec)
            ->will($this->returnValue($results));

        $this->assertSame($results, $this->repositoriesRepo->matching($spec));
    }

    public function testFindWhenTheRepositoryExists()
    {
        $repository = $this->getMockBuilder(Entity\Repository::class)->disableOriginalConstructor()->getMock();
        $qb = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $query = $this->getMockBuilder(AbstractQuery::class)->disableOriginalConstructor()->getMock();

        $qb->expects($this->once())
            ->method('where')
            ->will($this->returnSelf());

        $qb->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($query));

        $query->expects($this->once())
            ->method('getOneOrNullResult')
            ->will($this->returnValue($repository));

        $this->doctrineRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($qb));

        $this->assertSame($repository, $this->repositoriesRepo->find('some identifier'));
    }

    public function testFindWithRelations()
    {
        $repository = $this->getMockBuilder(Entity\Repository::class)->disableOriginalConstructor()->getMock();
        $qb = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $query = $this->getMockBuilder(AbstractQuery::class)->disableOriginalConstructor()->getMock();

        $qb->expects($this->once())
            ->method('where')
            ->will($this->returnSelf());

        $qb->expects($this->once())
            ->method('addSelect') // the relations
            ->will($this->returnSelf());

        $qb->expects($this->any())
            ->method('leftJoin') // same: the relations
            ->will($this->returnSelf());

        $qb->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($query));

        $query->expects($this->once())
            ->method('getOneOrNullResult')
            ->will($this->returnValue($repository));

        $this->doctrineRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($qb));

        $this->assertSame($repository, $this->repositoriesRepo->find('some identifier', Repositories::MODE_FETCH_RELATIONS));
    }

    /**
     * @expectedException \Regis\Domain\Repository\Exception\NotFound
     */
    public function testFindWhenTheRepositoryDoesNotExist()
    {
        $qb = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $query = $this->getMockBuilder(AbstractQuery::class)->disableOriginalConstructor()->getMock();

        $qb->expects($this->once())
            ->method('where')
            ->will($this->returnSelf());

        $qb->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($query));

        $query->expects($this->once())
            ->method('getOneOrNullResult')
            ->will($this->returnValue(null));

        $this->doctrineRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($qb));

        $this->repositoriesRepo->find('some identifier');
    }
}
