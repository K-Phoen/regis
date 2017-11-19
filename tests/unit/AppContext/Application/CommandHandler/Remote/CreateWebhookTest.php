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

namespace Tests\Regis\AppContext\Application\CommandHandler\Remote;

use PHPUnit\Framework\TestCase;
use Regis\AppContext\Application\Command;
use Regis\AppContext\Application\CommandHandler;
use Regis\AppContext\Application\Remote\ActionsRouter;
use Regis\AppContext\Domain\Entity;

class CreateWebhookTest extends TestCase
{
    private $actionsRouter;
    /** @var CommandHandler\Remote\CreateWebhook */
    private $handler;

    public function setUp()
    {
        $this->actionsRouter = $this->createMock(ActionsRouter::class);

        $this->handler = new CommandHandler\Remote\CreateWebhook($this->actionsRouter);
    }

    public function testItDelegatesTheWorkToTheActionRouter()
    {
        $repository = $this->createMock(Entity\Repository::class);
        $command = new Command\Remote\CreateWebhook($repository, 'hook-url');

        $this->actionsRouter->expects($this->once())
            ->method('createWebhook')
            ->with($repository, 'hook-url');

        $this->handler->handle($command);
    }
}
