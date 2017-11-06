<?php

namespace Tests\Regis\GithubContext\Application\CommandHandler\Team;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\CommandHandler;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository;

class AddMemberTest extends TestCase
{
    private $teamsRepo;
    private $usersRepo;
    /** @var CommandHandler\Team\AddMember */
    private $handler;

    public function setUp()
    {
        $this->teamsRepo = $this->createMock(Repository\Teams::class);
        $this->usersRepo = $this->createMock(Repository\Users::class);

        $this->handler = new CommandHandler\Team\AddMember($this->teamsRepo, $this->usersRepo);
    }

    public function testItAddsTheUserToTheTeam()
    {
        $owner = $this->createMock(Entity\User::class);
        $newMember = $this->createMock(Entity\User::class);
        $newMemberId = 'new-member-id';
        $team = new Entity\Team($owner, 'super team');

        $command = new Command\Team\AddMember($team, $newMemberId);

        $this->usersRepo->expects($this->once())
            ->method('findById')
            ->with($newMemberId)
            ->will($this->returnValue($newMember));

        $this->teamsRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\Team $team) use ($newMember) {
                return count($team->getMembers()) === 1
                && in_array($newMember, iterator_to_array($team->getMembers()), true);
            }));

        $this->handler->handle($command);
    }

    public function testItDoesNothingIfTheUserIsAlreadyInTheTeam()
    {
        $owner = $this->createMock(Entity\User::class);
        $newMember = $this->createMock(Entity\User::class);
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
