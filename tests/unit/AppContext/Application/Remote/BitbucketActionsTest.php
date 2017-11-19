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

namespace Tests\Regis\AppContext\Application\Remote;

use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;
use Regis\AppContext\Application\Remote\Actions;
use Regis\AppContext\Application\Remote\BitbucketActions;
use Regis\AppContext\Domain\Entity;
use Regis\BitbucketContext\Application\Command;

class BitbucketActionsTest extends TestCase
{
    private $bus;
    private $actions;

    public function setUp()
    {
        $this->bus = $this->createMock(CommandBus::class);

        $this->actions = new BitbucketActions($this->bus);
    }

    public function testItImplementsTheActionsInterface()
    {
        $this->assertInstanceOf(Actions::class, $this->actions);
    }

    public function testItDelegatesTheWebhookCreationToTheCommandBus()
    {
        $repository = $this->createMock(Entity\Repository::class);
        $repository->method('getIdentifier')->willReturn('repo-id');

        $this->bus->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (Command\Repository\CreateWebhook $command) {
                $this->assertSame('repo-id', $command->getRepository()->value());
                $this->assertSame('hook-url', $command->getCallbackUrl());

                return true;
            }));

        $this->actions->createWebhook($repository, 'hook-url');
    }
}
