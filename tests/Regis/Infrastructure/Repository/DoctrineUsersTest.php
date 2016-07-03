<?php

namespace Tests\Regis\Infrastructure\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

use Regis\Domain\Entity;
use Regis\Infrastructure\Repository\DoctrineUsers;

class DoctrineUsersTest extends \PHPUnit_Framework_TestCase
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var DoctrineUsers */
    private $usersRepo;

    public function setUp()
    {
        $this->em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();

        $this->usersRepo = new DoctrineUsers($this->em);
    }

    public function testSaveInspection()
    {
        $user = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();

        $this->em->expects($this->once())
            ->method('persist')
            ->with($user);
        $this->em->expects($this->once())
            ->method('flush');

        $this->usersRepo->save($user);
    }
}
