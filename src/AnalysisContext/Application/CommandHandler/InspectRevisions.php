<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
