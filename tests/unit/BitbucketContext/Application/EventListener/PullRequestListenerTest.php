<?php

namespace Tests\Regis\BitbucketContext\Application\EventListener;

use PHPUnit\Framework\TestCase;
use League\Tactician\CommandBus;
use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Application\Event;
use Regis\BitbucketContext\Application\EventListener\PullRequestListener;
use Regis\BitbucketContext\Domain\Model\PullRequest;
use Regis\Kernel\Event\DomainEventWrapper;

class PullRequestListenerTest extends TestCase
{
    private $bus;
    private $violationsCache;
    private $listener;

    public function setUp()
    {
        $this->bus = $this->createMock(CommandBus::class);

        $this->listener = new PullRequestListener($this->bus);
    }

    public function testItListensToTheRightEvents()
    {
        $listenedEvents = PullRequestListener::getSubscribedEvents();

        $this->assertArrayHasKey(Event\PullRequestOpened::class, $listenedEvents);
        $this->assertArrayHasKey(Event\PullRequestUpdated::class, $listenedEvents);
    }

    public function testItSendsTheRightCommandToTheBusWhenAPRIsOpened()
    {
        $pr = $this->createMock(PullRequest::class);
        $event = new DomainEventWrapper(new Event\PullRequestOpened($pr));

        $this->bus->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(Command\Inspection\SchedulePullRequest::class));

        $this->listener->onPullRequestUpdated($event);
    }

    public function testItSendsTheRightCommandToTheBusWhenAPRIsUpdated()
    {
        $pr = $this->createMock(PullRequest::class);
        $event = new DomainEventWrapper(new Event\PullRequestUpdated($pr, 'before', 'after'));

        $this->bus->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(Command\Inspection\SchedulePullRequest::class));

        $this->listener->onPullRequestUpdated($event);
    }
}
