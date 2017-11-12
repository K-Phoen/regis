<?php

namespace Tests\Regis\GithubContext\Application\CommandHandler\Team;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\CommandHandler;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository;

class RemoveRepositoryTest extends TestCase
{
    private $teamsRepo;
    private $reposRepo;
    /** @var CommandHandler\Team\RemoveRepository */
    private $handler;

    public function setUp()
    {
        $this->teamsRepo = $this->getMockBuilder(Repository\Teams::class)->getMock();
        $this->reposRepo = $this->getMockBuilder(Repository\Repositories::class)->getMock();

        $this->handler = new CommandHandler\Team\RemoveRepository($this->teamsRepo, $this->reposRepo);
    }

    public function testItRemovesTheRepositoryFromTheTeam()
    {
        $owner = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder(Entity\Repository::class)->disableOriginalConstructor()->getMock();
        $repoId = 'repo-id';

        $team = new Entity\Team($owner, 'super team');
        $team->addRepository($repo);

        $command = new Command\Team\RemoveRepository($team, $repoId);

        $this->reposRepo->expects($this->once())
            ->method('find')
            ->with($repoId)
            ->will($this->returnValue($repo));

        $this->teamsRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\Team $team) {
                return count($team->getRepositories()) === 0;
            }));

        $this->handler->handle($command);
    }
}
