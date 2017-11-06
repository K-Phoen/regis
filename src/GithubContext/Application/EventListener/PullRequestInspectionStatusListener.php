<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\EventListener;

use Regis\GithubContext\Domain\Repository\PullRequestInspections;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as UrlGenerator;

use Regis\Kernel\Event;
use Regis\GithubContext\Application\Events as GithubEvents;
use Regis\GithubContext\Application\Event as GithubEvent;
use Regis\Kernel\Events;
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
    private $inspectionsRepo;
    private $urlGenerator;

    public function __construct(ClientFactory $githubFactory, Repositories $repositoriesRepo, PullRequestInspections $inspectionsRepo, UrlGenerator $urlGenerator)
    {
        $this->githubFactory = $githubFactory;
        $this->repositoriesRepo = $repositoriesRepo;
        $this->inspectionsRepo = $inspectionsRepo;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents()
    {
        return [
            GithubEvents::PULL_REQUEST_OPENED => 'onPullRequestUpdated',
            GithubEvents::PULL_REQUEST_SYNCED => 'onPullRequestUpdated',

            Events::INSPECTION_STARTED => 'onInspectionStarted',
            Events::INSPECTION_FINISHED => 'onInspectionFinished',
            Events::INSPECTION_FAILED => 'onInspectionFailed',
        ];
    }

    public function onPullRequestUpdated(Event\DomainEventWrapper $event)
    {
        /** @var GithubEvent\PullRequestOpened|GithubEvent\PullRequestSynced $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $pullRequest = $domainEvent->getPullRequest();
        $repository = $this->findRepository($pullRequest->getRepositoryIdentifier()->getIdentifier());

        $this->setIntegrationStatus(
            $repository,
            $pullRequest->getHead(),
            new IntegrationStatus(Client::INTEGRATION_PENDING, 'Inspection scheduled.')
        );
    }

    public function onInspectionStarted(Event\DomainEventWrapper $event)
    {
        /** @var Event\InspectionStarted $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $inspection = $this->findPrInspection($domainEvent->getInspectionId());

        $this->setIntegrationStatus(
            $inspection->getRepository(),
            $inspection->getHead(),
            new IntegrationStatus(Client::INTEGRATION_PENDING, 'Inspection started…', $this->getInspectionUrl($inspection))
        );
    }

    public function onInspectionFinished(Event\DomainEventWrapper $event)
    {
        /** @var Event\InspectionFinished $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $inspection = $this->findPrInspection($domainEvent->getInspectionId());

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
            list($status, $message) = [Client::INTEGRATION_FAILURE, sprintf('Inspection with %d error(s) and %d warning(s).', $report->errorsCount(), $report->warningsCount())];
        } else {
            list($status, $message) = [Client::INTEGRATION_SUCCESS, 'Inspection successful.'];
        }

        $this->setIntegrationStatus(
            $inspection->getRepository(),
            $inspection->getHead(),
            new IntegrationStatus($status, $message, $this->getInspectionUrl($inspection))
        );
    }

    public function onInspectionFailed(Event\DomainEventWrapper $event)
    {
        /** @var Event\InspectionFailed $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $inspection = $this->findPrInspection($domainEvent->getInspectionId());

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

    private function findPrInspection(string $inspectionId): Entity\PullRequestInspection
    {
        return $this->inspectionsRepo->find($inspectionId);
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
