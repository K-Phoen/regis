<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Regis\GithubContext\Application\CommandHandler\Inspection;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\Github\ClientFactory;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Model\PullRequest;
use Regis\GithubContext\Domain\Repository;
use Regis\Kernel\Worker\MessagePublisher;

class SchedulePullRequest
{
    private $producer;
    private $repositoriesRepo;
    private $inspectionsRepo;
    private $clientFactory;

    public function __construct(MessagePublisher $producer, Repository\Repositories $repositoriesRepo, Repository\PullRequestInspections $inspectionsRepo, ClientFactory $clientFactory)
    {
        $this->producer = $producer;
        $this->repositoriesRepo = $repositoriesRepo;
        $this->inspectionsRepo = $inspectionsRepo;
        $this->clientFactory = $clientFactory;
    }

    public function handle(Command\Inspection\SchedulePullRequest $command): void
    {
        $pullRequest = $command->getPullRequest();
        /** @var Entity\Repository $repository */
        $repository = $this->repositoriesRepo->find($pullRequest->getRepositoryIdentifier()->getIdentifier());

        if (!$repository->isInspectionEnabled()) {
            return;
        }

        $number = $this->inspectionsRepo->nextBuildNumber($repository);

        // create the inspection
        $inspection = Entity\PullRequestInspection::create($repository, $pullRequest, $number);
        $this->inspectionsRepo->save($inspection);

        // and schedule it
        $this->producer->scheduleInspection([
            'inspection_id' => $inspection->getId(),
            'repository' => [
                'identifier' => $repository->getIdentifier(),
                'clone_url' => $this->findRepositoryCloneUrl($repository, $pullRequest),
            ],
            'revisions' => [
                'base' => $pullRequest->getBase(),
                'head' => $pullRequest->getHead(),
            ],
        ]);
    }

    private function findRepositoryCloneUrl(Entity\Repository $repository, PullRequest $pullRequest): string
    {
        $githubClient = $this->clientFactory->createForRepository($repository);

        $prDetails = $githubClient->getPullRequestDetails($repository->toIdentifier(), $pullRequest->getNumber());
        $repo = $prDetails['head']['repo'];
        $urlType = $repo['private'] ? 'ssh_url' : 'clone_url';

        return $repo[$urlType];
    }
}
