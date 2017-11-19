<?php

declare(strict_types=1);

namespace Tests\Regis\AnalysisContext\Application\CommandHandler;

use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Regis\AnalysisContext\Application\Command;
use Regis\AnalysisContext\Application\CommandHandler;
use Regis\AnalysisContext\Application\Event;
use Regis\AnalysisContext\Domain\Model;
use Regis\AnalysisContext\Domain\Entity;
use Regis\AnalysisContext\Domain\Repository\Inspections;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class InspectRevisionsTest extends TestCase
{
    const INSPECTION_ID = 'some-inspection-id';

    /** @var CommandBus */
    private $commandBus;

    /** @var Inspections */
    private $inspectionsRepo;

    /** @var Entity\Inspection */
    private $inspection;

    /** @var Model\Git\Repository */
    private $repository;

    /** @var Model\Git\Revisions */
    private $revisions;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var LoggerInterface */
    private $logger;

    /** @var CommandHandler\InspectRevisions */
    private $handler;

    public function setUp()
    {
        $this->commandBus = $this->createMock(CommandBus::class);
        $this->inspectionsRepo = $this->createMock(Inspections::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->inspection = $this->createMock(Entity\Inspection::class);
        $this->repository = $this->createMock(Model\Git\Repository::class);
        $this->revisions = $this->createMock(Model\Git\Revisions::class);

        $this->inspectionsRepo->method('find')->with(self::INSPECTION_ID)->willReturn($this->inspection);

        $this->handler = new CommandHandler\InspectRevisions(
            $this->commandBus, $this->inspectionsRepo, $this->dispatcher, $this->logger
        );
    }

    public function testWhenTheInspectionSucessfullyFinishes()
    {
        $report = $this->createMock(Entity\Report::class);

        $this->commandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(Command\RunAnalyses::class))
            ->willReturn($report);

        $this->inspectionsRepo
            ->expects($this->exactly(2))
            ->method('save')
            ->with($this->inspection);

        $this->inspection->expects($this->once())->method('start');
        $this->inspection->expects($this->once())->method('finish')->with($report);

        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [Event\InspectionStarted::class, $this->anything()],
                [Event\InspectionFinished::class, $this->anything()]
            );

        $this->handler->handle(new Command\InspectRevisions(self::INSPECTION_ID, $this->repository, $this->revisions));
    }

    public function testWhenTheInspectionFails()
    {
        $this->commandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(Command\RunAnalyses::class))
            ->willThrowException(new \RuntimeException('onoes'));

        $this->inspectionsRepo->expects($this->exactly(2))
            ->method('save')
            ->with($this->inspection);

        $this->inspection->expects($this->once())->method('start');
        $this->inspection->expects($this->once())->method('fail');

        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [Event\InspectionStarted::class, $this->anything()],
                [Event\InspectionFailed::class, $this->anything()]
            );

        $this->logger->expects($this->once())->method('warning');

        $this->handler->handle(new Command\InspectRevisions(self::INSPECTION_ID, $this->repository, $this->revisions));
    }
}
