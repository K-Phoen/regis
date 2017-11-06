<?php

namespace Tests\Regis\GithubContext\Application\EventListener;

use PHPUnit\Framework\TestCase;
use League\Tactician\CommandBus;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\EventListener\PullRequestReviewCommentsListener;
use Regis\GithubContext\Domain\Entity\PullRequestInspection;
use Regis\GithubContext\Domain\Repository\PullRequestInspections;
use Regis\Kernel\Event\DomainEventWrapper;
use Regis\Kernel\Event\InspectionFinished;
use Regis\Kernel\Events;

class PullRequestReviewCommentsListenerTest extends TestCase
{
    private $bus;
    private $inspectionRepo;
    private $listener;

    public function setUp()
    {
        $this->bus = $this->createMock(CommandBus::class);
        $this->inspectionRepo = $this->createMock(PullRequestInspections::class);

        $this->listener = new PullRequestReviewCommentsListener($this->bus, $this->inspectionRepo);
    }

    public function testItListensToTheRightEvents()
    {
        $listenedEvents = PullRequestReviewCommentsListener::getSubscribedEvents();

        $this->assertArrayHasKey(Events::INSPECTION_FINISHED, $listenedEvents);
    }

    public function testItSendsTheRightCommandToTheBus()
    {
        $inspection = $this->createMock(PullRequestInspection::class);
        $this->inspectionRepo->method('find')->with('inspection-id')->willReturn($inspection);

        $this->bus->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(Command\Inspection\SendViolationsAsComments::class));


        $this->listener->onInspectionFinished(new DomainEventWrapper(new InspectionFinished('inspection-id')));
    }
}
