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

namespace Tests\Regis\AppContext\Application\CommandHandler\Repository;

use PHPUnit\Framework\TestCase;
use Regis\AppContext\Application\Command;
use Regis\AppContext\Application\CommandHandler;
use Regis\AppContext\Domain\Entity;
use Regis\AppContext\Domain\Repository;

class EnableFlightModeTest extends TestCase
{
    private $repositoriesRepo;
    /** @var CommandHandler\Repository\EnableFlightMode */
    private $handler;

    public function setUp()
    {
        $this->repositoriesRepo = $this->createMock(Repository\Repositories::class);

        $this->handler = new CommandHandler\Repository\EnableFlightMode($this->repositoriesRepo);
    }

    public function testItRemovesTheUserFromTheTeam()
    {
        $owner = $this->createMock(Entity\User::class);
        $repo = new Entity\Repository($owner, Entity\Repository::TYPE_GITHUB, 'super/repo', 'repo name');

        $command = new Command\Repository\EnableFlightMode($repo);

        $this->repositoriesRepo->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Entity\Repository $repo) {
                $this->assertTrue($repo->isInspectionEnabled());

                return true;
            }));

        $this->handler->handle($command);
    }
}
