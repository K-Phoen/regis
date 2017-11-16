<?php

declare(strict_types=1);

namespace Tests\Regis\GithubContext\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Regis\GithubContext\Infrastructure\Repository\DoctrineRepositories;
use Regis\GithubContext\Domain\Entity;

class DoctrineRepositoriesTest extends TestCase
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var EntityRepository */
    private $doctrineRepository;
    /** @var DoctrineRepositories */
    private $repositoriesRepo;

    public function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->doctrineRepository = $this->createMock(EntityRepository::class);

        $this->em
            ->method('getRepository')
            ->with(Entity\Repository::class)
            ->willReturn($this->doctrineRepository);

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

    /**
     * @expectedException \Regis\GithubContext\Domain\Repository\Exception\NotFound
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
