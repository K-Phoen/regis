<?php

namespace Tests\Regis\Application\CommandHandler\Repository;

use Regis\Application\Repository\Repositories;
use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Application\Entity;

class UpdateConfigurationTest extends \PHPUnit_Framework_TestCase
{
    private $repositoriesRepo;
    private $repository;
    /** @var CommandHandler\Repository\UpdateConfiguration */
    private $handler;

    public function setUp()
    {
        $this->repositoriesRepo = $this->getMockBuilder(Repositories::class)->getMock();
        $this->repository = $this->getMockBuilder(Entity\Repository::class)->getMock();

        $this->handler = new CommandHandler\Repository\UpdateConfiguration($this->repositoriesRepo);
    }

    public function testItBuildsAndSaveTheEntity()
    {
        $command = new Command\Repository\UpdateConfiguration($this->repository, 'new shared secret');

        $this->repository->expects($this->once())
            ->method('newSharedSecret')
            ->with('new shared secret');

        $this->repositoriesRepo->expects($this->once())
            ->method('save')
            ->with($this->repository);

        $this->handler->handle($command);
    }
}
