<?php

namespace Tests\Regis\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use RulerZ\RulerZ;
use RulerZ\Spec\Specification;

use Regis\Domain\Entity;
use Regis\Infrastructure\Repository\DoctrineUsers;

class DoctrineUsersTest extends \PHPUnit_Framework_TestCase
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
        $this->em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $this->rulerz = $this->getMockBuilder(RulerZ::class)->disableOriginalConstructor()->getMock();
        $this->doctrineRepository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();

        $this->em->expects($this->any())
            ->method('getRepository')
            ->with(Entity\User::class)
            ->will($this->returnValue($this->doctrineRepository));

        $this->usersRepo = new DoctrineUsers($this->em, $this->rulerz);
    }

    public function testSaveUser()
    {
        $user = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();

        $this->em->expects($this->once())
            ->method('persist')
            ->with($user);
        $this->em->expects($this->once())
            ->method('flush');

        $this->usersRepo->save($user);
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

        $this->assertSame($results, $this->usersRepo->matching($spec));
    }

    public function testFindByIdWhenTheUserExists()
    {
        $user = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();

        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->will($this->returnValue($user));

        $this->assertSame($user, $this->usersRepo->findById('some identifier'));
    }

    /**
     * @expectedException \Regis\Domain\Repository\Exception\NotFound
     */
    public function testFindByIdWhenTheUserDoesNotExist()
    {
        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->will($this->returnValue(null));

        $this->usersRepo->findById('some identifier');
    }

    public function testFindByGithubIdWhenTheUserExists()
    {
        $user = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();

        $this->doctrineRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['githubId' => 42])
            ->will($this->returnValue($user));

        $this->assertSame($user, $this->usersRepo->findByGithubId(42));
    }

    /**
     * @expectedException \Regis\Domain\Repository\Exception\NotFound
     */
    public function testFindByGithubIdWhenTheUserDoesNotExist()
    {
        $this->doctrineRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['githubId' => 42])
            ->will($this->returnValue(null));

        $this->usersRepo->findByGithubId(42);
    }
}
