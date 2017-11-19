<?php

declare(strict_types=1);

namespace Tests\Regis\BitbucketContext\Infrastructure\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Regis\BitbucketContext\Infrastructure\Repository\DoctrineRepositories;
use Regis\BitbucketContext\Domain\Entity;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrineRepositoriesTest extends TestCase
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var RegistryInterface */
    private $registry;
    /** @var EntityRepository */
    private $doctrineRepository;
    /** @var DoctrineRepositories */
    private $repositoriesRepo;

    public function setUp()
    {
        $this->registry = $this->createMock(RegistryInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->doctrineRepository = $this->createMock(ObjectRepository::class);

        $this->registry->method('getManager')->willReturn($this->em);

        $this->em
            ->method('getRepository')
            ->with(Entity\Repository::class)
            ->willReturn($this->doctrineRepository);

        $this->repositoriesRepo = new DoctrineRepositories($this->registry);
    }

    public function testFindWhenTheRepositoryExists()
    {
        $repository = $this->createMock(Entity\Repository::class);

        $this->doctrineRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['identifier' => 'some identifier'])
            ->willReturn($repository);

        $this->assertSame($repository, $this->repositoriesRepo->find('some identifier'));
    }

    /**
     * @expectedException \Regis\BitbucketContext\Domain\Repository\Exception\NotFound
     */
    public function testFindWhenTheRepositoryDoesNotExist()
    {
        $this->doctrineRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['identifier' => 'some identifier'])
            ->willReturn(null);

        $this->repositoriesRepo->find('some identifier');
    }
}
