<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Github\Inspection;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

use Regis\Application\Command;
use Regis\Application\Entity;
use Regis\Application\Repository;

class SchedulePullRequest
{
    private $repositoriesRepo;
    private $inspectionsRepo;

    public function __construct(ProducerInterface $producer, Repository\Repositories $repositoriesRepo, Repository\Inspections $inspectionsRepo)
    {
        $this->producer = $producer;
        $this->repositoriesRepo = $repositoriesRepo;
        $this->inspectionsRepo = $inspectionsRepo;
    }

    public function handle(Command\Github\Inspection\SchedulePullRequest $command)
    {
        $pullRequest = $command->getPullRequest();
        /** @var Entity\Github\Repository $repository */
        $repository = $this->repositoriesRepo->find($pullRequest->getRepositoryIdentifier());

        // crreate the inspection
        $inspection = Entity\Github\PullRequestInspection::create($repository, $pullRequest);
        $this->inspectionsRepo->save($inspection);

        // and schedule it
        $this->producer->publish(json_encode([
            'inspection' => $inspection->getId(),
            'revisions' => $pullRequest->getRevisions()->toArray(),
            'pull_request' => $pullRequest->toArray(),
        ]));
    }
}