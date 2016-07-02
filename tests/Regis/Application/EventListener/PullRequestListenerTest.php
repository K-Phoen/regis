<?php

namespace Tests\Regis\Application\EventListener;

use League\Tactician\CommandBus;
use Regis\Application\Command;
use Regis\Application\Event;
use Regis\Application\EventListener\PullRequestListener;
use Regis\Application\Inspection\ViolationsCache;
use Regis\Domain\Entity\Inspection;
use Regis\Domain\Model\Github\PullRequest;

class PullRequestistenerTest extends \PHPUnit_Framework_TestCase
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
            ->with($this->callback(function($command) {
                return $command instanceof Command\Github\Inspection\SchedulePullRequest;
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
