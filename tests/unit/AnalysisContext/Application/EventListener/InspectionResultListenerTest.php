<?php

namespace Tests\Regis\AnalysisContext\Application\EventListener;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Application\EventListener\InspectionResultListener;
use Regis\AnalysisContext\Application\Event;
use Regis\AnalysisContext\Domain\Entity\Inspection;
use Regis\Kernel\Event\DomainEventWrapper;

class InspectionResultListenerTest extends TestCase
{
    private $producer;
    private $listener;

    public function setUp()
    {
        $this->producer = $this->createMock(ProducerInterface::class);

        $this->listener = new InspectionResultListener($this->producer);
    }

    public function testItListensToTheRightEvents()
    {
        $listenedEvents = InspectionResultListener::getSubscribedEvents();

        $this->assertArrayHasKey(Event\InspectionStarted::class, $listenedEvents);
        $this->assertArrayHasKey(Event\InspectionFinished::class, $listenedEvents);
        $this->assertArrayHasKey(Event\InspectionFailed::class, $listenedEvents);
    }

    public function testItPublishesAnEventWhenAnInspectionChangesItsStatus()
    {
        $inspection = $this->createMock(Inspection::class);
        $domainEvent = new Event\InspectionFinished($inspection);
        $event = new DomainEventWrapper($domainEvent);

        $inspection->method('id')->willReturn('inspection-id');
        $inspection->method('type')->willReturn('github_pr');

        $this->producer->expects($this->once())
            ->method('publish')
            ->with($this->callback(function ($payload) {
                $this->assertJson($payload);
                $this->assertJsonStringEqualsJsonString('{"inspection_id": "inspection-id"}', $payload);

                return true;
            }), 'analysis.github_pr.status');

        $this->listener->onInspectionStatus($event);
    }
}
