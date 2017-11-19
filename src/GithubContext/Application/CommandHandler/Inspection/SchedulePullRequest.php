<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\CommandHandler\Inspection;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\Github\ClientFactory;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Model\PullRequest;
use Regis\GithubContext\Domain\Repository;

class SchedulePullRequest
{
    private $producer;
    private $repositoriesRepo;
    private $inspectionsRepo;
    private $clientFactory;

    public function __construct(ProducerInterface $producer, Repository\Repositories $repositoriesRepo, Repository\PullRequestInspections $inspectionsRepo, ClientFactory $clientFactory)
    {
        $this->producer = $producer;
        $this->repositoriesRepo = $repositoriesRepo;
        $this->inspectionsRepo = $inspectionsRepo;
        $this->clientFactory = $clientFactory;
    }

    public function handle(Command\Inspection\SchedulePullRequest $command)
    {
        $pullRequest = $command->getPullRequest();
        /** @var Entity\Repository $repository */
        $repository = $this->repositoriesRepo->find($pullRequest->getRepositoryIdentifier()->getIdentifier());

        if (!$repository->isInspectionEnabled()) {
            return;
        }

        // create the inspection
        $inspection = Entity\PullRequestInspection::create($repository, $pullRequest);
        $this->inspectionsRepo->save($inspection);

        // FIXME probably broken for forked repositories

        // and schedule it
        $this->producer->publish(json_encode([
            'inspection_id' => $inspection->getId(),
            'repository' => [
                'identifier' => $repository->getIdentifier(),
                'clone_url' => $this->findRepositoryCloneUrl($repository, $pullRequest),
            ],
            'revisions' => [
                'base' => $pullRequest->getBase(),
                'head' => $pullRequest->getHead(),
            ],
        ]));
    }

    private function findRepositoryCloneUrl(Entity\Repository $repository, PullRequest $pullRequest): string
    {
        $githubClient = $this->clientFactory->createForRepository($repository);

        $prDetails = $githubClient->getPullRequestDetails($repository->toIdentifier(), $pullRequest->getNumber());

        return $prDetails['head']['repo']['private'] ? $prDetails['head']['repo']['ssh_url'] : $prDetails['head']['repo']['clone_url'];
    }
}
