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

namespace Regis\BitbucketContext\Application\CommandHandler\Inspection;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Application\Bitbucket\ClientFactory;
use Regis\BitbucketContext\Domain\Entity;
use Regis\BitbucketContext\Domain\Repository;

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

    public function handle(Command\Inspection\SchedulePullRequest $command): void
    {
        $pullRequest = $command->getPullRequest();
        /** @var Entity\Repository $repository */
        $repository = $this->repositoriesRepo->find($pullRequest->getRepository()->value());

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
                'clone_url' => $this->findRepositoryCloneUrl($repository),
            ],
            'revisions' => [
                'base' => $pullRequest->getBase(),
                'head' => $pullRequest->getHead(),
            ],
        ]));
    }

    private function findRepositoryCloneUrl(Entity\Repository $repository): string
    {
        $bitbucketClient = $this->clientFactory->createForRepository($repository);

        return $bitbucketClient->getCloneUrl($repository->toIdentifier());
    }
}
