<?php

namespace Tests\Regis\Application\CommandHandler\Team;

use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Domain\Entity;
use Regis\Domain\Repository;

class AddMemberTest extends \PHPUnit_Framework_TestCase
{
    private $teamsRepo;
    private $usersRepo;
    /** @var CommandHandler\Team\AddMember */
    private $handler;

    public function setUp()
    {
        $this->teamsRepo = $this->getMockBuilder(Repository\Teams::class)->getMock();
        $this->usersRepo = $this->getMockBuilder(Repository\Users::class)->getMock();

        $this->handler = new CommandHandler\Team\AddMember($this->teamsRepo, $this->usersRepo);
    }

    public function testItAddsTheUserToTheTeam()
    {
        $owner = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $newMember = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $newMemberId = 'new-member-id';
        $team = new Entity\Team($owner, 'super team');

        $command = new Command\Team\AddMember($team, $newMemberId);

        $this->usersRepo->expects($this->once())
            ->method('findById')
            ->with($newMemberId)
            ->will($this->returnValue($newMember));

        $this->teamsRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function(Entity\Team $team) use ($newMember) {
                return count($team->getMembers()) === 1
                && in_array($newMember, iterator_to_array($team->getMembers()), true);
            }));

        $this->handler->handle($command);
    }

    public function testItDoesNothingIfTheUserIsAlreadyInTheTeam()
    {
        $owner = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $newMember = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $newMemberId = 'new-member-id';
        $team = new Entity\Team($owner, 'super team');

        $command = new Command\Team\AddMember($team, $newMemberId);

        $this->usersRepo->expects($this->once())
            ->method('findById')
            ->with($newMemberId)
            ->will($this->returnValue($newMember));

        $this->teamsRepo->expects($this->once())
            ->method('save')
            ->will($this->throwException(new Repository\Exception\UniqueConstraintViolation()));

        $this->handler->handle($command);
    }
}
