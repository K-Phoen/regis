<?php

declare(strict_types=1);

namespace Tests\Regis\BitbucketContext\Application\Worker;

use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Application\Worker\AnalysisStatusRunner;
use Regis\BitbucketContext\Domain\Entity\PullRequestInspection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Regis\Kernel\Event\DomainEventWrapper;
use Regis\BitbucketContext\Domain\Entity\Inspection;
use Regis\BitbucketContext\Domain\Repository\PullRequestInspections;
use Regis\BitbucketContext\Application\Event;

class AnalysisStatusRunnerTest extends TestCase
{
    const INSPECTION_ID = 'inspection-id';

    private $inspectionsRepo;
    private $dispatcher;
    private $worker;

    public function setUp()
    {
        $this->inspectionsRepo = $this->createMock(PullRequestInspections::class);
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->worker = new AnalysisStatusRunner($this->inspectionsRepo, $this->dispatcher);
    }

    public function testItConvertsInspectionStartedEvents()
    {
        $message = $this->amqpMessage(self::INSPECTION_ID);
        $inspection = $this->inspection(Inspection::STATUS_STARTED);

        $this->inspectionsRepo->method('find')->with(self::INSPECTION_ID)->willReturn($inspection);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(Event\InspectionStarted::class, $this->callback(function (DomainEventWrapper $event) use ($inspection) {
                $this->assertInstanceOf(Event\InspectionStarted::class, $event->getDomainEvent());
                $this->assertSame($inspection, $event->getDomainEvent()->getInspection());

                return true;
            }));

        $this->worker->execute($message);
    }

    public function testItConvertsInspectionFinishedEvents()
    {
        $message = $this->amqpMessage(self::INSPECTION_ID);
        $inspection = $this->inspection(Inspection::STATUS_FINISHED);

        $this->inspectionsRepo->method('find')->with(self::INSPECTION_ID)->willReturn($inspection);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(Event\InspectionFinished::class, $this->callback(function (DomainEventWrapper $event) use ($inspection) {
                $this->assertInstanceOf(Event\InspectionFinished::class, $event->getDomainEvent());
                $this->assertSame($inspection, $event->getDomainEvent()->getInspection());

                return true;
            }));

        $this->worker->execute($message);
    }

    public function testItConvertsInspectionFailedEvents()
    {
        $message = $this->amqpMessage(self::INSPECTION_ID);
        $inspection = $this->inspection(Inspection::STATUS_FAILED);

        $this->inspectionsRepo->method('find')->with(self::INSPECTION_ID)->willReturn($inspection);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(Event\InspectionFailed::class, $this->callback(function (DomainEventWrapper $event) use ($inspection) {
                $this->assertInstanceOf(Event\InspectionFailed::class, $event->getDomainEvent());
                $this->assertSame($inspection, $event->getDomainEvent()->getInspection());

                return true;
            }));

        $this->worker->execute($message);
    }

    public function testItRaisesAnErrorForUnknownInspectionStatuses()
    {
        $message = $this->amqpMessage(self::INSPECTION_ID);
        $inspection = $this->inspection('unknown status');

        $this->inspectionsRepo->method('find')->with(self::INSPECTION_ID)->willReturn($inspection);

        $this->dispatcher->expects($this->never())->method('dispatch');
        $this->expectException(\LogicException::class);

        $this->worker->execute($message);
    }

    private function amqpMessage(string $inspectionId)
    {
        return new AMQPMessage(json_encode([
            'inspection_id' => $inspectionId,
        ]));
    }

    private function inspection(string $status): PullRequestInspection
    {
        $inspection = $this->createMock(PullRequestInspection::class);

        $inspection->method('getStatus')->willReturn($status);

        return $inspection;
    }
}
