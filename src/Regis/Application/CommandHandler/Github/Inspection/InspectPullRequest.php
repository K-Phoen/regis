<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Github\Inspection;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

use Regis\Application\Command;
use Regis\Application\Event;
use Regis\Application\Inspector;
use Regis\Domain\Repository;

class InspectPullRequest
{
    private $inspector;
    private $dispatcher;
    private $inspectionsRepo;
    private $logger;

    public function __construct(EventDispatcher $dispatcher, Inspector $inspector, Repository\Inspections $inspectionsRepo, LoggerInterface $logger)
    {
        $this->dispatcher = $dispatcher;
        $this->inspector = $inspector;
        $this->inspectionsRepo = $inspectionsRepo;
        $this->logger = $logger;
    }

    public function handle(Command\Github\Inspection\InspectPullRequest $command)
    {
        $inspection = $command->getInspection();
        $pullRequest = $command->getPullRequest();

        $inspection->start();
        $this->inspectionsRepo->save($inspection);

        $this->dispatch(Event::INSPECTION_STARTED, new Event\InspectionStarted($inspection, $pullRequest));

        try {
            $report = $this->inspector->inspect($pullRequest->getRepository(), $pullRequest->getRevisions());
            $inspection->finish();
            $this->dispatch(Event::INSPECTION_FINISHED, new Event\InspectionFinished($inspection, $pullRequest, $report));
        } catch (\Exception $e) {
            $inspection->fail($e);
            $this->dispatch(Event::INSPECTION_FAILED, new Event\InspectionFailed($inspection, $pullRequest, $e));

            $this->logger->warning('Inspection {inspection_id} failed', [
                'inspection_id' => $inspection->getId(),
                'error_class' => get_class($e),
                'error_message' => $e->getMessage(),
                'error_backtrace' => $e->getTraceAsString(),
            ]);
        } finally {
            $this->inspectionsRepo->save($inspection);
        }
    }

    private function dispatch(string $eventName, Event $event)
    {
        $this->dispatcher->dispatch($eventName, new Event\DomainEventWrapper($event));
    }
}
