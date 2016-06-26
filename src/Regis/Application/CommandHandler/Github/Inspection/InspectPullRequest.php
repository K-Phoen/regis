<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Github\Inspection;

use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

use Regis\Bundle\WebhooksBundle\Event\DomainEventWrapper;

use Regis\Application\Command;
use Regis\Application\Entity;
use Regis\Application\Event;
use Regis\Application\Inspector;
use Regis\Application\Repository\Inspections;

class InspectPullRequest
{
    private $inspector;
    private $dispatcher;
    private $inspectionsRepo;

    public function __construct(EventDispatcher $dispatcher, Inspector $inspector, Inspections $inspectionsRepo)
    {
        $this->dispatcher = $dispatcher;
        $this->inspector = $inspector;
        $this->inspectionsRepo = $inspectionsRepo;
    }

    public function handle(Command\Github\Inspection\InspectPullRequest $command)
    {
        $inspection = $command->getInspection();
        $pullRequest = $command->getPullRequest();

        $inspection->start();
        $this->inspectionsRepo->save($inspection);

        $this->dispatch(Event::INSPECTION_STARTED, new Event\InspectionStarted($inspection, $pullRequest));

        try {
            $reportSummary = $this->inspector->inspect($pullRequest);
            $inspection->finish();
            $this->dispatch(Event::INSPECTION_FINISHED, new Event\InspectionFinished($inspection, $pullRequest, $reportSummary));
        } catch (\Exception $e) {
            $inspection->fail($e);
            $this->dispatch(Event::INSPECTION_FAILED, new Event\InspectionFailed($inspection, $pullRequest, $e));
            // TODO why throwing the exception? Logging it + reporting it should be better.
            throw $e;
        } finally {
            $this->inspectionsRepo->save($inspection);
        }
    }

    private function dispatch(string $eventName, Event $event)
    {
        $this->dispatcher->dispatch($eventName, new DomainEventWrapper($event));
    }
}