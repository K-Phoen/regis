<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application\Worker;

use League\Tactician\CommandBus;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Regis\Event\Events;
use Regis\Event as DomainEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;
use Psr\Log\LoggerInterface as Logger;
use Regis\AnalysisContext\Application\Command;
use Regis\AnalysisContext\Application\Event;
use Regis\AnalysisContext\Domain\Model;

class InspectionRunner implements ConsumerInterface
{
    private $commandBus;
    private $dispatcher;
    private $logger;

    public function __construct(CommandBus $commandBus, EventDispatcher $dispatcher,  Logger $logger)
    {
        $this->commandBus = $commandBus;
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    public function execute(AMQPMessage $msg)
    {
        $event = json_decode($msg->getBody(), true);

        $inspectionId = $event['inspection_id'];
        $repository = Model\Git\Repository::fromArray($event['repository']);
        $revisions = Model\Git\Revisions::fromArray($event['revisions']);

        $this->logger->info('Inspection {inspection_id} started', [
            'inspection_id' => $inspectionId,
        ]);
        $this->dispatch(Events::INSPECTION_STARTED, new Event\InspectionStarted($inspectionId));

        // TODO the report and violations should be persisted in this context and read in the others
        // as we have the inspection ID, it should be feasible.
        // This means that an "Inspection" entity exists in this context (read/write entity) and another one exists
        // in the Github context (read only this time).
        // It should allow this worker to work with other frontends (bitbucket/gitlab)
        try {
            $report = $this->commandBus->handle(new Command\InspectRevisions($repository, $revisions));

            $this->logger->info('Inspection {inspection_id} finished', [
                'inspection_id' => $inspectionId,
            ]);
            $this->dispatch(Events::INSPECTION_FINISHED, new Event\InspectionFinished($inspectionId, $report));
        } catch (\Exception $e) {
            $this->dispatch(Events::INSPECTION_FAILED, new Event\InspectionFailed($inspectionId, $e));

            $this->logger->warning('Inspection {inspection_id} failed', [
                'inspection_id' => $inspectionId,
                'worker_data' => $event,
                'error_class' => get_class($e),
                'error_message' => $e->getMessage(),
                'error_backtrace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function dispatch(string $eventName, Events $event)
    {
        $this->dispatcher->dispatch($eventName, new DomainEvent\DomainEventWrapper($event));
    }
}
