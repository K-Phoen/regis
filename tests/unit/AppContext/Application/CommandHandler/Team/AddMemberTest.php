<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Tests\Regis\AppContext\Application\CommandHandler\Team;

use PHPUnit\Framework\TestCase;
use Regis\AppContext\Application\Command;
use Regis\AppContext\Application\CommandHandler;
use Regis\AppContext\Domain\Entity;
use Regis\AppContext\Domain\Repository;

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
