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

namespace Tests\Regis\BitbucketContext\Application\EventListener;

use PHPUnit\Framework\TestCase;
use League\Tactician\CommandBus;
use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Application\Event;
use Regis\BitbucketContext\Application\EventListener\PullRequestClosedListener;
use Regis\BitbucketContext\Domain\Model\PullRequest;
use Regis\Kernel\Event\DomainEventWrapper;

class PullRequestClosedListenerTest extends TestCase
{
    private $bus;
    private $listener;

    public function setUp()
    {
        $this->bus = $this->createMock(CommandBus::class);

        $this->listener = new PullRequestClosedListener($this->bus);
    }

    public function testItListensToTheRightEvents()
    {
        $listenedEvents = PullRequestClosedListener::getSubscribedEvents();

        $this->assertArrayHasKey(Event\PullRequestRejected::class, $listenedEvents);
        $this->assertArrayHasKey(Event\PullRequestMerged::class, $listenedEvents);
    }

    public function testItClearsTheViolationsCacheWhenAPRIsRejected()
    {
        $pr = $this->createMock(PullRequest::class);
        $event = new DomainEventWrapper(new Event\PullRequestRejected($pr));

        $this->bus->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(Command\Inspection\ClearViolationsCache::class));

        $this->listener->onPullRequestClosed($event);
    }

    public function testItClearsTheViolationsCacheWhenAPRIsMerged()
    {
        $pr = $this->createMock(PullRequest::class);
        $event = new DomainEventWrapper(new Event\PullRequestMerged($pr));

        $this->bus->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(Command\Inspection\ClearViolationsCache::class));

        $this->listener->onPullRequestClosed($event);
    }
}
