<?php

declare(strict_types=1);

namespace Tests\Regis\AppContext\Application\CommandHandler\Team;

use PHPUnit\Framework\TestCase;
use Regis\AppContext\Application\Command;
use Regis\AppContext\Application\CommandHandler;
use Regis\AppContext\Domain\Entity;
use Regis\AppContext\Domain\Repository;

class AddRepositoryTest extends TestCase
{
    private $teamsRepo;
    private $reposRepo;
    /** @var CommandHandler\Team\AddRepository */
    private $handler;

    public function setUp()
    {
        $this->teamsRepo = $this->getMockBuilder(Repository\Teams::class)->getMock();
        $this->reposRepo = $this->getMockBuilder(Repository\Repositories::class)->getMock();

        $this->handler = new CommandHandler\Team\AddRepository($this->teamsRepo, $this->reposRepo);
    }

    public function testItAddsTheRepositoryToTheTeam()
    {
        $owner = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $newRepo = $this->getMockBuilder(Entity\Repository::class)->disableOriginalConstructor()->getMock();
        $newRepoId = 'new-repo-id';
        $team = new Entity\Team($owner, 'super team');

        $command = new Command\Team\AddRepository($team, $newRepoId);

        $this->reposRepo->expects($this->once())
            ->method('find')
            ->with($newRepoId)
            ->will($this->returnValue($newRepo));

        $this->teamsRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\Team $team) use ($newRepo) {
                return count($team->getRepositories()) === 1
                && in_array($newRepo, iterator_to_array($team->getRepositories()), true);
            }));

        $this->handler->handle($command);
    }

    public function testItDoesNothingIfTheRepositoryIsAlreadyInTheTeam()
    {
        $owner = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $newRepo = $this->getMockBuilder(Entity\Repository::class)->disableOriginalConstructor()->getMock();
        $newRepoId = 'new-repo-id';
        $team = new Entity\Team($owner, 'super team');

        $command = new Command\Team\AddRepository($team, $newRepoId);

        $this->reposRepo->expects($this->once())
            ->method('find')
            ->with($newRepoId)
            ->will($this->returnValue($newRepo));

        $this->teamsRepo->expects($this->once())
            ->method('save')
            ->will($this->throwException(new Repository\Exception\UniqueConstraintViolation()));

        $this->handler->handle($command);
    }
}
