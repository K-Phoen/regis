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

namespace Regis\GithubContext\Application\EventListener;

use Regis\Kernel\Event\DomainEventWrapper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as UrlGenerator;
use Regis\GithubContext\Application\Event;
use Regis\GithubContext\Application\Github\Client;
use Regis\GithubContext\Application\Github\ClientFactory;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository\Repositories;
use Regis\GithubContext\Application\Github\IntegrationStatus;

/**
 * TODO this class should rely on the command bus
 */
class PullRequestInspectionStatusListener implements EventSubscriberInterface
{
    private $githubFactory;
    private $repositoriesRepo;
    private $urlGenerator;

    public function __construct(ClientFactory $githubFactory, Repositories $repositoriesRepo, UrlGenerator $urlGenerator)
    {
        $this->githubFactory = $githubFactory;
        $this->repositoriesRepo = $repositoriesRepo;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents()
    {
        return [
            Event\PullRequestOpened::class => 'onPullRequestUpdated',
            Event\PullRequestSynced::class => 'onPullRequestUpdated',

            Event\InspectionStarted::class => 'onInspectionStarted',
            Event\InspectionFinished::class => 'onInspectionFinished',
            Event\InspectionFailed::class => 'onInspectionFailed',
        ];
    }

    public function onPullRequestUpdated(DomainEventWrapper $event)
    {
        /** @var Event\PullRequestOpened|Event\PullRequestSynced $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $pullRequest = $domainEvent->getPullRequest();
        $repository = $this->findRepository($pullRequest->getRepositoryIdentifier()->getIdentifier());

        $this->setIntegrationStatus(
            $repository,
            $pullRequest->getHead(),
            new IntegrationStatus(Client::INTEGRATION_PENDING, 'Inspection scheduled.')
        );
    }

    public function onInspectionStarted(DomainEventWrapper $event)
    {
        /** @var Event\InspectionStarted $domainEvent */
        $domainEvent = $event->getDomainEvent();
        $inspection = $domainEvent->getInspection();

        $this->setIntegrationStatus(
            $inspection->getRepository(),
            $inspection->getHead(),
            new IntegrationStatus(Client::INTEGRATION_PENDING, 'Inspection started…', $this->getInspectionUrl($inspection))
        );
    }

    public function onInspectionFinished(DomainEventWrapper $event)
    {
        /** @var Event\InspectionFinished $domainEvent */
        $domainEvent = $event->getDomainEvent();
        $inspection = $domainEvent->getInspection();

        if (!$inspection->hasReport()) {
            $this->setIntegrationStatus(
                $inspection->getRepository(),
                $inspection->getHead(),
                new IntegrationStatus(Client::INTEGRATION_FAILURE, 'Internal error.', $this->getInspectionUrl($inspection))
            );

            return;
        }

        $report = $inspection->getReport();

        if ($report->hasErrors() || $report->hasWarnings()) {
            list($status, $message) = [Client::INTEGRATION_FAILURE, sprintf('Inspection finished with %d error(s) and %d warning(s).', $report->errorsCount(), $report->warningsCount())];
        } else {
            list($status, $message) = [Client::INTEGRATION_SUCCESS, 'Inspection successful.'];
        }

        $this->setIntegrationStatus(
            $inspection->getRepository(),
            $inspection->getHead(),
            new IntegrationStatus($status, $message, $this->getInspectionUrl($inspection))
        );
    }

    public function onInspectionFailed(DomainEventWrapper $event)
    {
        /** @var Event\InspectionFailed $domainEvent */
        $domainEvent = $event->getDomainEvent();
        $inspection = $domainEvent->getInspection();

        $this->setIntegrationStatus(
            $inspection->getRepository(),
            $inspection->getHead(),
            new IntegrationStatus(Client::INTEGRATION_ERROR, 'Inspection failed.', $this->getInspectionUrl($inspection))
        );
    }

    private function setIntegrationStatus(Entity\Repository $repository, string $head, IntegrationStatus $status)
    {
        if (!$repository->isInspectionEnabled()) {
            return;
        }

        $client = $this->githubFactory->createForRepository($repository);
        $client->setIntegrationStatus($repository->toIdentifier(), $head, $status);
    }

    private function findRepository(string $repositoryId): Entity\Repository
    {
        return $this->repositoriesRepo->find($repositoryId);
    }

    private function getInspectionUrl(Entity\Inspection $inspection): string
    {
        return $this->urlGenerator->generate('inspection_detail', ['id' => $inspection->getId()], UrlGenerator::ABSOLUTE_URL);
    }
}
