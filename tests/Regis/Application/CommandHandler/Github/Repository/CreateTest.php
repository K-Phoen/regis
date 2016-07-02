<?php

namespace Tests\Regis\Application\CommandHandler\Github\Repository;

use Regis\Application\Repository\Repositories;
use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Domain\Entity;

class CreateTest extends \PHPUnit_Framework_TestCase
{
    private $repositoriesRepo;
    /** @var CommandHandler\Github\Repository\Create */
    private $handler;

    public function setUp()
    {
        $this->repositoriesRepo = $this->getMockBuilder(Repositories::class)->getMock();

        $this->handler = new CommandHandler\Github\Repository\Create($this->repositoriesRepo);
    }

    public function testItBuildsAndSaveTheEntity()
    {
        $command = new Command\Github\Repository\Create('some identifier', 'shared secret');

        $this->repositoriesRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function(Entity\Repository $repository) {
                return $repository->getIdentifier() === 'some identifier' &&
                       $repository->getSharedSecret() === 'shared secret';
            }));

        $this->handler->handle($command);
    }
}
