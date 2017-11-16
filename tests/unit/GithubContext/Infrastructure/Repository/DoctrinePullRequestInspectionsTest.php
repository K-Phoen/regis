<?php

declare(strict_types=1);

namespace Tests\Regis\GithubContext\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Regis\GithubContext\Infrastructure\Repository\DoctrinePullRequestInspections;
use Regis\GithubContext\Domain\Entity;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrinePullRequestInspectionsTest extends TestCase
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var RegistryInterface */
    private $registry;
    /** @var ObjectRepository */
    private $doctrineRepository;
    /** @var DoctrinePullRequestInspections */
    private $inspectionsRepo;

    public function setUp()
    {
        $this->registry = $this->createMock(RegistryInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->doctrineRepository = $this->createMock(ObjectRepository::class);

        $this->registry->method('getManager')->willReturn($this->em);

        $this->em
            ->method('getRepository')
            ->with(Entity\PullRequestInspection::class)
            ->willReturn($this->doctrineRepository);

        $this->inspectionsRepo = new DoctrinePullRequestInspections($this->registry);
    }

    public function testSaveInspection()
    {
        $inspection = $this->createMock(Entity\PullRequestInspection::class);

        $this->em->expects($this->once())
            ->method('persist')
            ->with($inspection);
        $this->em->expects($this->once())
            ->method('flush');

        $this->inspectionsRepo->save($inspection);
    }

    public function testFindWhenTheInspectionExists()
    {
        $inspection = $this->createMock(Entity\PullRequestInspection::class);

        $this->doctrineRepository->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->willReturn($inspection);

        $this->assertSame($inspection, $this->inspectionsRepo->find('some identifier'));
    }

    /**
     * @expectedException \Regis\GithubContext\Domain\Repository\Exception\NotFound
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
