<?php

namespace Tests\Regis\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use RulerZ\RulerZ;
use RulerZ\Spec\Specification;

use Regis\Domain\Entity;
use Regis\Infrastructure\Repository\DoctrineTeams;

class DoctrineTeamsTest extends TestCase
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var EntityRepository */
    private $doctrineRepository;
    /** @var RulerZ */
    private $rulerz;
    /** @var DoctrineTeams */
    private $teamsRepo;

    public function setUp()
    {
        $this->em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $this->rulerz = $this->getMockBuilder(RulerZ::class)->disableOriginalConstructor()->getMock();
        $this->doctrineRepository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();

        $this->em->expects($this->any())
            ->method('getRepository')
            ->with(Entity\Team::class)
            ->will($this->returnValue($this->doctrineRepository));

        $this->teamsRepo = new DoctrineTeams($this->em, $this->rulerz);
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

        $this->assertSame($results, $this->teamsRepo->matching($spec));
    }

    public function testSaveTeam()
    {
        $team = $this->getMockBuilder(Entity\Team::class)->disableOriginalConstructor()->getMock();

        $this->em->expects($this->once())
            ->method('persist')
            ->with($team);
        $this->em->expects($this->once())
            ->method('flush');

        $this->teamsRepo->save($team);
    }

    /**
     * @expectedException \Regis\Domain\Repository\Exception\UniqueConstraintViolation
     */
    public function testSaveTeamWrapsUniqueConstraintsViolations()
    {
        $team = $this->getMockBuilder(Entity\Team::class)->disableOriginalConstructor()->getMock();
        $exception = $this->getMockBuilder(UniqueConstraintViolationException::class)->disableOriginalConstructor()->getMock();

        $this->em->expects($this->once())
            ->method('flush')
            ->will($this->throwException($exception));

        $this->teamsRepo->save($team);
    }

    public function testFindWhenTheTeamExists()
    {
        $team = $this->getMockBuilder(Entity\Team::class)->disableOriginalConstructor()->getMock();

        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->will($this->returnValue($team));

        $this->assertSame($team, $this->teamsRepo->find('some identifier'));
    }

    /**
     * @expectedException \Regis\Domain\Repository\Exception\NotFound
     */
    public function testFindWhenTheTeamDoesNotExist()
    {
        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->will($this->returnValue(null));

        $this->teamsRepo->find('some identifier');
    }
}
