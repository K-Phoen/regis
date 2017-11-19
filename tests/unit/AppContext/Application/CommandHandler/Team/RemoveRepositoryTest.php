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

class RemoveRepositoryTest extends TestCase
{
    private $teamsRepo;
    private $reposRepo;
    /** @var CommandHandler\Team\RemoveRepository */
    private $handler;

    public function setUp()
    {
        $this->teamsRepo = $this->createMock(Repository\Teams::class);
        $this->reposRepo = $this->createMock(Repository\Repositories::class);

        $this->handler = new CommandHandler\Team\RemoveRepository($this->teamsRepo, $this->reposRepo);
    }

    public function testItRemovesTheRepositoryFromTheTeam()
    {
        $owner = $this->createMock(Entity\User::class);
        $repo = $this->createMock(Entity\Repository::class);
        $repoId = 'repo-id';

        $team = new Entity\Team($owner, 'super team');
        $team->addRepository($repo);

        $command = new Command\Team\RemoveRepository($team, $repoId);

        $this->reposRepo->expects($this->once())
            ->method('find')
            ->with($repoId)
            ->willReturn($repo);

        $this->teamsRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\Team $team) {
                return count($team->getRepositories()) === 0;
            }));

        $this->handler->handle($command);
    }
}
