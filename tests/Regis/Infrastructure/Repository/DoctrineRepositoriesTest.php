<?php

namespace Tests\Regis\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
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
        $this->doctrineRepository =$this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
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

        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->will($this->returnValue($repository));

        $this->assertSame($repository, $this->repositoriesRepo->find('some identifier'));
    }

    /**
     * @expectedException \Regis\Domain\Repository\Exception\NotFound
     */
    public function testFindWhenTheRepositoryDoesNotExist()
    {
        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->will($this->returnValue(null));

        $this->repositoriesRepo->find('some identifier');
    }
}
