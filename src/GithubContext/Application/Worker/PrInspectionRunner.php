<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Worker;

use League\Tactician\CommandBus;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Entity\PullRequestInspection;
use Regis\GithubContext\Domain\Model\PullRequest;
use Regis\GithubContext\Domain\Repository;

class PrInspectionRunner implements ConsumerInterface
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

        $command = new Command\Inspection\InspectPullRequest($inspection, $pullRequest);
        $this->commandBus->handle($command);
    }
}
