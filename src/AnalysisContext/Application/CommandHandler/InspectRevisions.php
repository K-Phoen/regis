<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application\CommandHandler;

use League\Tactician\CommandBus;
use Regis\AnalysisContext\Domain\Repository\Inspections;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Psr\Log\LoggerInterface as Logger;
use Regis\AnalysisContext\Application\Command;
use Regis\Kernel\Event;
use Regis\Kernel\Events;

class InspectRevisions
{
    private $commandBus;
    private $inspectionsRepo;
    private $dispatcher;
    private $logger;

    public function __construct(CommandBus $commandBus, Inspections $inspectionsRepo, EventDispatcher $dispatcher,  Logger $logger)
    {
        $this->commandBus = $commandBus;
        $this->inspectionsRepo = $inspectionsRepo;
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    public function handle(Command\InspectRevisions $command)
    {
        $inspectionId = $command->getInspectionId();
        $inspection = $this->inspectionsRepo->find($inspectionId);

        $this->logger->info('Inspection {inspection_id} started', [
            'inspection_id' => $inspectionId,
        ]);

        $inspection->start();
        $this->inspectionsRepo->save($inspection);

        $this->dispatch(Events::INSPECTION_STARTED, new Event\InspectionStarted($inspectionId));

        try {
            $report = $this->commandBus->handle(new Command\RunAnalyses($command->getRepository(), $command->getRevisions()));

            $inspection->finish($report);

            $this->logger->info('Inspection {inspection_id} finished', [
                'inspection_id' => $inspectionId,
            ]);
            $this->dispatch(Events::INSPECTION_FINISHED, new Event\InspectionFinished($inspectionId));
        } catch (\Exception $e) {
            $inspection->fail($e);
            $this->dispatch(Events::INSPECTION_FAILED, new Event\InspectionFailed($inspectionId, $e));

            $this->logger->warning('Inspection {inspection_id} failed', [
                'inspection_id' => $command->getInspectionId(),
                'repository' => $command->getRepository()->toArray(),
                'revisions' => $command->getRevisions()->toArray(),
                'error_class' => get_class($e),
                'error_message' => $e->getMessage(),
                'error_backtrace' => $e->getTraceAsString(),
            ]);
        } finally {
            $this->inspectionsRepo->save($inspection);
        }
    }

    private function dispatch(string $eventName, Events $event)
    {
        $this->dispatcher->dispatch($eventName, new Event\DomainEventWrapper($event));
    }
}
