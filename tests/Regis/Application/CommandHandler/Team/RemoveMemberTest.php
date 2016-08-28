<?php

namespace Tests\Regis\Application\CommandHandler\Team;

use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Domain\Entity;
use Regis\Domain\Repository;

class RemoveMemberTest extends \PHPUnit_Framework_TestCase
{
    private $teamsRepo;
    private $usersRepo;
    /** @var CommandHandler\Team\RemoveMember */
    private $handler;

    public function setUp()
    {
        $this->teamsRepo = $this->getMockBuilder(Repository\Teams::class)->getMock();
        $this->usersRepo = $this->getMockBuilder(Repository\Users::class)->getMock();

        $this->handler = new CommandHandler\Team\RemoveMember($this->teamsRepo, $this->usersRepo);
    }

    public function testItRemovesTheUserFromTheTeam()
    {
        $owner = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $member = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $memberId = 'member-id';

        $team = new Entity\Team($owner, 'super team');
        $team->addMember($member);

        $command = new Command\Team\RemoveMember($team, $memberId);

        $this->usersRepo->expects($this->once())
            ->method('findById')
            ->with($memberId)
            ->will($this->returnValue($member));

        $this->teamsRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function(Entity\Team $team) {
                return count($team->getMembers()) === 0;
            }));

        $this->handler->handle($command);
    }
}
