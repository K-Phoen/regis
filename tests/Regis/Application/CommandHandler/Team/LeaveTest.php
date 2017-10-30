<?php

namespace Tests\Regis\Application\CommandHandler\Team;

use PHPUnit\Framework\TestCase;
use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Domain\Entity;
use Regis\Domain\Repository;

class LeaveTest extends TestCase
{
    private $teamsRepo;
    /** @var CommandHandler\Team\RemoveMember */
    private $handler;

    public function setUp()
    {
        $this->teamsRepo = $this->getMockBuilder(Repository\Teams::class)->getMock();

        $this->handler = new CommandHandler\Team\Leave($this->teamsRepo);
    }

    public function testItRemovesTheUserFromTheTeam()
    {
        $owner = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $user = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();

        $team = new Entity\Team($owner, 'super team');
        $team->addMember($user);

        $command = new Command\Team\Leave($team, $user);

        $this->teamsRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\Team $team) {
                return count($team->getMembers()) === 0;
            }));

        $this->handler->handle($command);
    }
}
