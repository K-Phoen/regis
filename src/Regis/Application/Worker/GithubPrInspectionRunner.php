<?php

declare(strict_types=1);

namespace Regis\Application\Worker;

use League\Tactician\CommandBus;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

use Regis\Application\Command;
use Regis\Application\Repository;
use Regis\Domain\Entity\Github\PullRequestInspection;
use Regis\Domain\Model\Github\PullRequest;

class GithubPrInspectionRunner implements ConsumerInterface
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
        $event = json_decode($msg->getBody(), true);
        /** @var PullRequestInspection $inspection */
        $inspection = $this->inspectionsRepo->find($event['inspection']);
        $pullRequest = PullRequest::fromArray($event['pull_request']);

        $command = new Command\Github\Inspection\InspectPullRequest($inspection, $pullRequest);
        $this->commandBus->handle($command);
    }
}