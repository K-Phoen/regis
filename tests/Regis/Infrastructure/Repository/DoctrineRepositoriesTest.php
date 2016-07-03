<?php

namespace Tests\Regis\Infrastructure\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

use Regis\Infrastructure\Repository\DoctrineRepositories;
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
        $repository = $this->getMockBuilder(Entity\Repository::class)->disableOriginalConstructor()->getMock();

        $this->em->expects($this->once())
            ->method('persist')
            ->with($repository);
        $this->em->expects($this->once())
            ->method('flush');

        $this->repositoriesRepo->save($repository);
    }

    public function testFindForUser()
    {
        $user = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $repository = $this->getMockBuilder(Entity\Repository::class)->disableOriginalConstructor()->getMock();
        $allRepositories = [$repository];

        $this->doctrineRepository->expects($this->once())
            ->method('findBy')
            ->with(['owner' => $user])
            ->will($this->returnValue($allRepositories));

        $this->assertEquals($allRepositories, iterator_to_array($this->repositoriesRepo->findForUser($user)));
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
