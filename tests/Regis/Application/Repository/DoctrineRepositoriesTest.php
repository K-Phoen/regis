<?php

namespace Tests\Regis\Application\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

use Regis\Application\Repository\DoctrineRepositories;
use Regis\Domain\Entity;

class DoctrineRepositoriesTest extends \PHPUnit_Framework_TestCase
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var ObjectRepository */
    private $doctrineRepository;
    /** @var DoctrineRepositories */
    private $repositoriesRepo;

    public function setUp()
    {
        $this->em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $this->doctrineRepository =$this->getMockBuilder(ObjectRepository::class)->getMock();

        $this->em->expects($this->any())
            ->method('getRepository')
            ->with(Entity\Repository::class)
            ->will($this->returnValue($this->doctrineRepository));

        $this->repositoriesRepo = new DoctrineRepositories($this->em);
    }

    public function testSaveRepository()
    {
        $repository = $this->getMockBuilder(Entity\Repository::class)->getMock();

        $this->em->expects($this->once())
            ->method('persist')
            ->with($repository);
        $this->em->expects($this->once())
            ->method('flush');

        $this->repositoriesRepo->save($repository);
    }

    public function testFindAll()
    {
        $repository = $this->getMockBuilder(Entity\Repository::class)->getMock();
        $allRepositories = [$repository];

        $this->doctrineRepository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($allRepositories));

        $this->assertEquals($allRepositories, iterator_to_array($this->repositoriesRepo->findAll()));
    }

    public function testFindWhenTheRepositoryExists()
    {
        $repository = $this->getMockBuilder(Entity\Repository::class)->getMock();

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
