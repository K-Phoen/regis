<?php

namespace Tests\Regis\Application\CommandHandler\Repository;

use PHPUnit\Framework\TestCase;
use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Domain\Entity;
use Regis\Domain\Repository;

class DisableInspectionsTest extends TestCase
{
    private $repositoriesRepo;
    /** @var CommandHandler\Repository\DisableInspections */
    private $handler;

    public function setUp()
    {
        $this->repositoriesRepo = $this->getMockBuilder(Repository\Repositories::class)->getMock();

        $this->handler = new CommandHandler\Repository\DisableInspections($this->repositoriesRepo);
    }

    public function testItRemovesTheUserFromTheTeam()
    {
        $owner = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $repo = new Entity\Github\Repository($owner, 'super/repo');

        $command = new Command\Repository\DisableInspections($repo);

        $this->repositoriesRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\Repository $repo) {
                return !$repo->isInspectionEnabled();
            }));

        $this->handler->handle($command);
    }
}
