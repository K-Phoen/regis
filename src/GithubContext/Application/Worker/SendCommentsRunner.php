<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Worker;

use League\Tactician\CommandBus;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Repository\PullRequestInspections;

class SendCommentsRunner implements ConsumerInterface
{
    private $commandBus;
    private $inspectionsRepo;

    public function __construct(CommandBus $commandBus, PullRequestInspections $inspectionsRepo)
    {
        $this->commandBus = $commandBus;
        $this->inspectionsRepo = $inspectionsRepo;
    }

    public function execute(AMQPMessage $msg)
    {
        $event = json_decode($msg->getBody(), true);

        $inspection = $this->inspectionsRepo->find($event['inspection_id']);

        $this->commandBus->handle(new Command\Inspection\SendViolationsAsComments($inspection));
    }
}
