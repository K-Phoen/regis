<?php

namespace Tests\Regis\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

use Regis\Infrastructure\Repository\DoctrineInspections;
use Regis\Domain\Entity;

class DoctrineInspectionsTest extends TestCase
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var ObjectRepository */
    private $doctrineRepository;
    /** @var DoctrineInspections */
    private $inspectionsRepo;

    public function setUp()
    {
        $this->em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $this->doctrineRepository = $this->getMockBuilder(ObjectRepository::class)->getMock();

        $this->em
            ->method('getRepository')
            ->with(Entity\Inspection::class)
            ->willReturn($this->doctrineRepository);

        $this->inspectionsRepo = new DoctrineInspections($this->em);
    }

    public function testSaveInspection()
    {
        $inspection = $this->getMockBuilder(Entity\Inspection::class)->disableOriginalConstructor()->getMock();

        $this->em->expects($this->once())
            ->method('persist')
            ->with($inspection);
        $this->em->expects($this->once())
            ->method('flush');

        $this->inspectionsRepo->save($inspection);
    }

    public function testFindWhenTheInspectionExists()
    {
        $inspection = $this->getMockBuilder(Entity\Inspection::class)->disableOriginalConstructor()->getMock();

        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->will($this->returnValue($inspection));

        $this->assertSame($inspection, $this->inspectionsRepo->find('some identifier'));
    }

    /**
     * @expectedException \Regis\Domain\Repository\Exception\NotFound
     */
    public function testFindWhenTheInspectionDoesNotExist()
    {
        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->will($this->returnValue(null));

        $this->inspectionsRepo->find('some identifier');
    }
}
