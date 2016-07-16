<?php

namespace Tests\Regis\Application\CommandHandler\Github\Repository;

use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Application\Random;
use Regis\Domain\Entity;
use Regis\Domain\Repository\Exception;
use Regis\Domain\Repository\Repositories;

class CreateTest extends \PHPUnit_Framework_TestCase
{
    private $repositoriesRepo;
    private $randomGenerator;
    /** @var CommandHandler\Github\Repository\Create */
    private $handler;

    public function setUp()
    {
        $this->repositoriesRepo = $this->getMockBuilder(Repositories::class)->getMock();
        $this->randomGenerator = $this->getMockBuilder(Random\Generator::class)->getMock();

        $this->handler = new CommandHandler\Github\Repository\Create($this->repositoriesRepo, $this->randomGenerator);
    }

    public function testItBuildsAndSaveTheEntityIfItDoesNotAlreadyExists()
    {
        $user = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $command = new Command\Github\Repository\Create($user, 'some identifier', 'shared secret');

        $this->repositoriesRepo->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->will($this->throwException(new Exception\NotFound()));

        $this->repositoriesRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function(Entity\Repository $repository) use ($user) {
                return $repository->getOwner() === $user &&
                $repository->getIdentifier() === 'some identifier' &&
                $repository->getSharedSecret() === 'shared secret';
            }));

        $repository = $this->handler->handle($command);

        $this->assertInstanceOf(Entity\Github\Repository::class, $repository);
    }

    public function testItReturnsTheRepositoryIfItAlreadyExists()
    {
        $user = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $command = new Command\Github\Repository\Create($user, 'some identifier', 'shared secret');
        $repository = $this->getMockBuilder(Entity\Repository::class)->disableOriginalConstructor()->getMock();

        $this->repositoriesRepo->expects($this->once())
            ->method('find')
            ->with('some identifier')
            ->will($this->returnValue($repository));

        $this->assertSame($repository, $this->handler->handle($command));
    }
}
