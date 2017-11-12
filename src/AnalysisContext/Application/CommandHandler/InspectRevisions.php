<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application\CommandHandler;

use League\Tactician\CommandBus;
use Regis\AnalysisContext\Domain\Repository\Inspections;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Psr\Log\LoggerInterface as Logger;
use Regis\AnalysisContext\Application\Command;
use Regis\AnalysisContext\Application\Event;
use Regis\Kernel\Event as KernelEvent;

class InspectRevisions
{
    private $commandBus;
    private $inspectionsRepo;
    private $dispatcher;
    private $logger;

    public function __construct(CommandBus $commandBus, Inspections $inspectionsRepo, EventDispatcher $dispatcher, Logger $logger)
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

        $this->dispatch(new Event\InspectionStarted($inspection));

        try {
            $report = $this->commandBus->handle(new Command\RunAnalyses($command->getRepository(), $command->getRevisions()));

            $inspection->finish($report);
            $this->inspectionsRepo->save($inspection);

            $this->logger->info('Inspection {inspection_id} finished', [
                'inspection_id' => $inspectionId,
            ]);
            $this->dispatch(new Event\InspectionFinished($inspection));
        } catch (\Exception $e) {
            $this->logger->warning('Inspection {inspection_id} failed', [
                'inspection_id' => $command->getInspectionId(),
                'repository' => $command->getRepository()->toArray(),
                'revisions' => $command->getRevisions()->toArray(),
                'error_class' => get_class($e),
                'error_message' => $e->getMessage(),
                'error_backtrace' => $e->getTraceAsString(),
            ]);

            $inspection->fail($e);
            $this->inspectionsRepo->save($inspection);

            $this->dispatch(new Event\InspectionFailed($inspection, $e));
        }
    }

    private function dispatch($event)
    {
        $this->dispatcher->dispatch(get_class($event), new KernelEvent\DomainEventWrapper($event));
    }
}
