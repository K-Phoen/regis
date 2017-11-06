<?php

namespace Tests\Regis\GithubContext\Application\EventListener;

use PHPUnit\Framework\TestCase;
use League\Tactician\CommandBus;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\Event;
use Regis\GithubContext\Application\EventListener\PullRequestListener;
use Regis\GithubContext\Application\Inspection\ViolationsCache;
use Regis\GithubContext\Domain\Model\PullRequest;

class PullRequestListenerTest extends TestCase
{
    private $bus;
    private $violationsCache;
    private $listener;

    public function setUp()
    {
        $this->bus = $this->getMockBuilder(CommandBus::class)->disableOriginalConstructor()->getMock();
        $this->violationsCache = $this->getMockBuilder(ViolationsCache::class)->disableOriginalConstructor()->getMock();

        $this->listener = new PullRequestListener($this->bus, $this->violationsCache);
    }

    public function testItListensToTheRightEvents()
    {
        $listenedEvents = PullRequestListener::getSubscribedEvents();

        $this->assertArrayHasKey(Event::PULL_REQUEST_OPENED, $listenedEvents);
        $this->assertArrayHasKey(Event::PULL_REQUEST_SYNCED, $listenedEvents);
        $this->assertArrayHasKey(Event::PULL_REQUEST_CLOSED, $listenedEvents);
    }

    public function testItSendsTheRightCommandToTheBusWhenAPRIsCreated()
    {
        $pr = $this->getMockBuilder(PullRequest::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\PullRequestOpened($pr);
        $event = new Event\DomainEventWrapper($domainEvent);

        $this->bus->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($command) {
                return $command instanceof Command\Inspection\SchedulePullRequest;
            }));

        $this->listener->onPullRequestUpdated($event);
    }

    public function testItCleansTheViolationCacheWhenAPRIsClosed()
    {
        $pr = $this->getMockBuilder(PullRequest::class)->disableOriginalConstructor()->getMock();
        $domainEvent = new Event\PullRequestClosed($pr);
        $event = new Event\DomainEventWrapper($domainEvent);

        $this->violationsCache->expects($this->once())
            ->method('clear')
            ->with($pr);

        $this->listener->onPullRequestClosed($event);
    }
}
