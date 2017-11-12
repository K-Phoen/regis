<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application\Worker;

use League\Tactician\CommandBus;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Regis\AnalysisContext\Application\Command;
use Regis\AnalysisContext\Domain\Model;

class InspectionRunner implements ConsumerInterface
{
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function execute(AMQPMessage $msg)
    {
        $event = json_decode($msg->getBody(), true);

        $repository = Model\Git\Repository::fromArray($event['repository']);
        $revisions = Model\Git\Revisions::fromArray($event['revisions']);

        $this->commandBus->handle(new Command\InspectRevisions($event['inspection_id'], $repository, $revisions));
    }
}
