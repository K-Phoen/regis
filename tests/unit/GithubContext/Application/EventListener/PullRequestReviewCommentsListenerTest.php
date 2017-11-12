<?php

namespace Tests\Regis\GithubContext\Application\EventListener;

use PHPUnit\Framework\TestCase;
use League\Tactician\CommandBus;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\Event;
use Regis\GithubContext\Application\EventListener\PullRequestReviewCommentsListener;
use Regis\GithubContext\Domain\Entity\PullRequestInspection;
use Regis\Kernel\Event\DomainEventWrapper;

class PullRequestReviewCommentsListenerTest extends TestCase
{
    private $bus;
    private $listener;

    public function setUp()
    {
        $this->bus = $this->createMock(CommandBus::class);

        $this->listener = new PullRequestReviewCommentsListener($this->bus);
    }

    public function testItListensToTheRightEvents()
    {
        $listenedEvents = PullRequestReviewCommentsListener::getSubscribedEvents();

        $this->assertArrayHasKey(Event\InspectionFinished::class, $listenedEvents);
    }

    public function testItSendsTheRightCommandToTheBus()
    {
        $inspection = $this->createMock(PullRequestInspection::class);

        $this->bus->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(Command\Inspection\SendViolationsAsComments::class));

        $this->listener->onInspectionFinished(new DomainEventWrapper(new Event\InspectionFinished($inspection)));
    }
}
