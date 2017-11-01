<?php

namespace Tests\Regis\Application\CommandHandler\Github\Repository;

use PHPUnit\Framework\TestCase;
use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Domain\Entity;
use Regis\Domain\Repository\Repositories;

class UpdateConfigurationTest extends TestCase
{
    private $repositoriesRepo;
    private $repository;
    /** @var CommandHandler\Github\Repository\DefineSharedSecret */
    private $handler;

    public function setUp()
    {
        $this->repositoriesRepo = $this->getMockBuilder(Repositories::class)->getMock();
        $this->repository = $this->getMockBuilder(Entity\Github\Repository::class)->disableOriginalConstructor()->getMock();

        $this->handler = new CommandHandler\Github\Repository\DefineSharedSecret($this->repositoriesRepo);
    }

    public function testItBuildsAndSaveTheEntity()
    {
        $command = new Command\Github\Repository\UpdateConfiguration($this->repository, 'new shared secret');

        $this->repository->expects($this->once())
            ->method('newSharedSecret')
            ->with('new shared secret');

        $this->repositoriesRepo->expects($this->once())
            ->method('save')
            ->with($this->repository);

        $this->handler->handle($command);
    }
}
