<?php

declare(strict_types=1);

namespace Tests\Regis\BitbucketContext\Application\EventListener;

use PHPUnit\Framework\TestCase;
use League\Tactician\CommandBus;
use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Application\Event;
use Regis\BitbucketContext\Application\EventListener\PullRequestReviewCommentsListener;
use Regis\BitbucketContext\Domain\Entity\PullRequestInspection;
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
