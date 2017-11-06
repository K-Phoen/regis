<?php

namespace Tests\Regis\GithubContext\Application\CommandHandler\Team;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\CommandHandler;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository;

class CreateTest extends TestCase
{
    private $teamsRepo;
    /** @var CommandHandler\Team\Create */
    private $handler;

    public function setUp()
    {
        $this->teamsRepo = $this->getMockBuilder(Repository\Teams::class)->getMock();

        $this->handler = new CommandHandler\Team\Create($this->teamsRepo);
    }

    public function testItCreatesATeam()
    {
        $owner = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $teamName = 'super team';

        $command = new Command\Team\Create($owner, $teamName);

        $this->teamsRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\Team $team) use ($owner, $teamName) {
                return $team->getOwner() === $owner
                    && $team->getName() === $teamName
                    && $team->getId();
            }));

        $this->handler->handle($command);
    }
}
