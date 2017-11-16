<?php

declare(strict_types=1);

namespace Tests\Regis\GithubContext\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Infrastructure\Repository\DoctrineUsers;

class DoctrineUsersTest extends TestCase
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var EntityRepository */
    private $doctrineRepository;
    /** @var DoctrineUsers */
    private $usersRepo;

    public function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->doctrineRepository = $this->createMock(EntityRepository::class);

        $this->em
            ->method('getRepository')
            ->with(Entity\GithubDetails::class)
            ->willReturn($this->doctrineRepository);

        $this->usersRepo = new DoctrineUsers($this->em);
    }

    public function testSaveUser()
    {
        $user = $this->createMock(Entity\GithubDetails::class);

        $this->em->expects($this->once())
            ->method('persist')
            ->with($user);
        $this->em->expects($this->once())
            ->method('flush');

        $this->usersRepo->save($user);
    }
}
