<?php

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
