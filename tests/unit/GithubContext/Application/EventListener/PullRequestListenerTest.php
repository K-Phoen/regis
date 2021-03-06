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

namespace Tests\Regis\GithubContext\Application\EventListener;

use PHPUnit\Framework\TestCase;
use League\Tactician\CommandBus;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\Event;
use Regis\GithubContext\Application\EventListener\PullRequestListener;
use Regis\GithubContext\Application\Inspection\ViolationsCache;
use Regis\GithubContext\Domain\Model\PullRequest;
use Regis\Kernel\Event\DomainEventWrapper;

class PullRequestListenerTest extends TestCase
{
    private $bus;
    private $violationsCache;
    private $listener;

    public function setUp()
    {
        $this->bus = $this->createMock(CommandBus::class);
        $this->violationsCache = $this->createMock(ViolationsCache::class);

        $this->listener = new PullRequestListener($this->bus, $this->violationsCache);
    }

    public function testItListensToTheRightEvents()
    {
        $listenedEvents = PullRequestListener::getSubscribedEvents();

        $this->assertArrayHasKey(Event\PullRequestOpened::class, $listenedEvents);
        $this->assertArrayHasKey(Event\PullRequestSynced::class, $listenedEvents);
        $this->assertArrayHasKey(Event\PullRequestClosed::class, $listenedEvents);
    }

    public function testItSendsTheRightCommandToTheBusWhenAPRIsCreated()
    {
        $pr = $this->createMock(PullRequest::class);
        $event = new DomainEventWrapper(new Event\PullRequestClosed($pr));

        $this->bus->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(Command\Inspection\SchedulePullRequest::class));

        $this->listener->onPullRequestUpdated($event);
    }

    public function testItCleansTheViolationCacheWhenAPRIsClosed()
    {
        $pr = $this->createMock(PullRequest::class);
        $event = new DomainEventWrapper(new Event\PullRequestClosed($pr));

        $this->violationsCache->expects($this->once())
            ->method('clear')
            ->with($pr);

        $this->listener->onPullRequestClosed($event);
    }
}
