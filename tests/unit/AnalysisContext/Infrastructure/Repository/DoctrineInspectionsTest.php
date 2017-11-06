<?php

namespace Tests\Regis\AnalysisContext\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

use Regis\AnalysisContext\Infrastructure\Repository\DoctrineInspections;
use Regis\AnalysisContext\Domain\Entity;

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
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->doctrineRepository = $this->createMock(ObjectRepository::class);

        $this->em
            ->method('getRepository')
            ->with(Entity\Inspection::class)
            ->willReturn($this->doctrineRepository);

        $this->inspectionsRepo = new DoctrineInspections($this->em);
    }

    public function testSaveInspection()
    {
        $inspection = $this->createMock(Entity\Inspection::class);

        $this->em->expects($this->once())
            ->method('persist')
            ->with($inspection);
        $this->em->expects($this->once())
            ->method('flush');

        $this->inspectionsRepo->save($inspection);
    }

    public function testFindWhenTheInspectionExists()
    {
        $inspection = $this->createMock(Entity\Inspection::class);

        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->willReturn($inspection);

        $this->assertSame($inspection, $this->inspectionsRepo->find('some identifier'));
    }

    /**
     * @expectedException \Regis\AnalysisContext\Domain\Repository\Exception\NotFound
     */
    public function testFindWhenTheInspectionDoesNotExist()
    {
        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->willReturn(null);

        $this->inspectionsRepo->find('some identifier');
    }
}
