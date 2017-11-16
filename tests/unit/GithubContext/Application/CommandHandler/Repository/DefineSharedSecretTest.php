<?php

declare(strict_types=1);

namespace Tests\Regis\GithubContext\Application\CommandHandler\Repository;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\CommandHandler;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository\Repositories;

class DefineSharedSecretTest extends TestCase
{
    private $repositoriesRepo;
    private $repository;
    /** @var CommandHandler\Repository\DefineSharedSecret */
    private $handler;

    public function setUp()
    {
        $this->repositoriesRepo = $this->createMock(Repositories::class);
        $this->repository = $this->createMock(Entity\Repository::class);

        $this->handler = new CommandHandler\Repository\DefineSharedSecret($this->repositoriesRepo);
    }

    public function testItBuildsAndSaveTheEntity()
    {
        $command = new Command\Repository\DefineSharedSecret($this->repository, 'new shared secret');

        $this->repository->expects($this->once())
            ->method('newSharedSecret')
            ->with('new shared secret');

        $this->repositoriesRepo->expects($this->once())
            ->method('save')
            ->with($this->repository);

        $this->handler->handle($command);
    }
}
