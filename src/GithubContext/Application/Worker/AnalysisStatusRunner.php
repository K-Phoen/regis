<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Worker;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Regis\GithubContext\Domain\Entity\Inspection;
use Regis\GithubContext\Domain\Repository\PullRequestInspections;
use Regis\GithubContext\Application\Event;
use Regis\Kernel\Event\DomainEventWrapper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

class AnalysisStatusRunner implements ConsumerInterface
{
    private $inspectionsRepo;
    private $dispatcher;

    public function __construct(PullRequestInspections $inspectionsRepo, EventDispatcher $dispatcher)
    {
        $this->inspectionsRepo = $inspectionsRepo;
        $this->dispatcher = $dispatcher;
    }

    public function execute(AMQPMessage $msg)
    {
        $event = json_decode($msg->getBody(), true);
        $inspection = $this->inspectionsRepo->find($event['inspection_id']);

        switch ($inspection->getStatus()) {
            case Inspection::STATUS_STARTED:
                $domainEvent = new Event\InspectionStarted($inspection);

                break;
            case Inspection::STATUS_FINISHED:
                $domainEvent = new Event\InspectionFinished($inspection);

                break;
            case Inspection::STATUS_FAILED:
                $domainEvent = new Event\InspectionFailed($inspection);

                break;
            default:
                throw new \LogicException(sprintf('Unknown inspection status: "%s"', $inspection->getStatus()));
        }

        $this->dispatcher->dispatch(get_class($domainEvent), new DomainEventWrapper($domainEvent));
    }
}
