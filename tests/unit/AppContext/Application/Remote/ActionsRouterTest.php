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

use PHPUnit\Framework\TestCase;
use Regis\AppContext\Application\Remote\Actions;
use Regis\AppContext\Application\Remote\ActionsRouter;
use Regis\AppContext\Domain\Entity;

class ActionsRouterTest extends TestCase
{
    public function testItImplementsTheActionsInterface()
    {
        $router = new ActionsRouter([]);

        $this->assertInstanceOf(Actions::class, $router);
    }

    public function testItDelegatesTheWebhookCreationToTheRightImplementation()
    {
        $githubActions = $this->createMock(Actions::class);
        $bitbucketActions = $this->createMock(Actions::class);
        $repository = $this->createMock(Entity\Repository::class);

        $repository->method('getType')->willReturn(Entity\Repository::TYPE_BITBUCKET);

        $router = new ActionsRouter([
            'github' => $githubActions,
            'bitbucket' => $bitbucketActions,
        ]);

        $githubActions->expects($this->never())->method('createWebhook');
        $bitbucketActions->expects($this->once())->method('createWebhook')->with($repository, 'hook-url');

        $router->createWebhook($repository, 'hook-url');
    }

    public function testItThrowsAnErrorIfNoImplementationIsFound()
    {
        $repository = $this->createMock(Entity\Repository::class);
        $repository->method('getType')->willReturn(Entity\Repository::TYPE_BITBUCKET);
        $router = new ActionsRouter([]);

        $this->expectException(\LogicException::class);

        $router->createWebhook($repository, 'hook-url');
    }
}
