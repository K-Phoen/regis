<?php

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
