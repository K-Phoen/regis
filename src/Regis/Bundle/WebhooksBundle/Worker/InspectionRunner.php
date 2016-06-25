<?php

declare(strict_types=1);

namespace Regis\Bundle\WebhooksBundle\Worker;

use League\Tactician\CommandBus;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

use Regis\Application\Command;
use Regis\Application\Model\Github\PullRequest;
use Regis\Application\Repository;

class InspectionRunner implements ConsumerInterface
{
    private $commandBus;
    private $inspectionsRepo;

    public function __construct(CommandBus $commandBus, Repository\Inspections $inspectionsRepo)
    {
        $this->commandBus = $commandBus;
        $this->inspectionsRepo = $inspectionsRepo;
    }

    public function execute(AMQPMessage $msg)
    {
        $event = json_decode($msg->body, true);
        $inspection = $this->inspectionsRepo->find($event['inspection']);
        $pullRequest = PullRequest::fromArray($event['pull_request']);

        $command = new Command\Inspection\Start($inspection, $pullRequest);
        $this->commandBus->handle($command);
    }
}