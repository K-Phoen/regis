<?php

declare(strict_types=1);

namespace Regis\Application\EventListener;

use Regis\Application\Github\IntegrationStatus;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as UrlGenerator;

use Regis\Application\Event;
use Regis\Application\Github\Client;
use Regis\Application\Github\ClientFactory;
use Regis\Domain\Entity;
use Regis\Domain\Model\Github\PullRequest;
use Regis\Domain\Repository\Repositories;

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
            // @TODO There should be a domain event for "PR inspection scheduled"
            Event::PULL_REQUEST_OPENED => 'onPullRequestUpdated',
            Event::PULL_REQUEST_SYNCED => 'onPullRequestUpdated',

            Event::INSPECTION_STARTED => 'onInspectionStarted',
            Event::INSPECTION_FINISHED => 'onInspectionFinished',
            Event::INSPECTION_FAILED => 'onInspectionFailed',
        ];
    }

    public function onPullRequestUpdated(Event\DomainEventWrapper $event)
    {
        /** @var Event\PullRequestOpened|Event\PullRequestSynced $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->setIntegrationStatus($domainEvent->getPullRequest(), new IntegrationStatus(Client::INTEGRATION_PENDING, 'Inspection scheduled.'));
    }

    public function onInspectionStarted(Event\DomainEventWrapper $event)
    {
        /** @var Event\InspectionStarted $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->setIntegrationStatus($domainEvent->getPullRequest(), new IntegrationStatus(Client::INTEGRATION_PENDING, 'Inspection started…', $this->getInspectionUrl($domainEvent->getInspection())));
    }

    public function onInspectionFinished(Event\DomainEventWrapper $event)
    {
        /** @var Event\InspectionFinished $domainEvent */
        $domainEvent = $event->getDomainEvent();
        $report = $domainEvent->getReport();

        if ($report->hasErrors() || $report->hasWarnings()) {
            list($status, $message) = [Client::INTEGRATION_FAILURE, sprintf('Inspection with %d error(s) and %d warning(s).', $report->errorsCount(), $report->warningsCount())];
        } else {
            list($status, $message) = [Client::INTEGRATION_SUCCESS, 'Inspection successful.'];
        }

        $this->setIntegrationStatus($domainEvent->getPullRequest(), new IntegrationStatus($status, $message, $this->getInspectionUrl($domainEvent->getInspection())));
    }

    public function onInspectionFailed(Event\DomainEventWrapper $event)
    {
        /** @var Event\InspectionFailed $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->setIntegrationStatus($domainEvent->getPullRequest(), new IntegrationStatus(Client::INTEGRATION_ERROR, 'Inspection failed.', $this->getInspectionUrl($domainEvent->getInspection())));
    }

    private function setIntegrationStatus(PullRequest $pullRequest, IntegrationStatus $status)
    {
        /** @var Entity\Github\Repository $repository */
        $repository = $this->repositoriesRepo->find($pullRequest->getRepositoryIdentifier());

        if (!$repository->isInspectionEnabled()) {
            return;
        }

        $client = $this->githubFactory->createForRepository($repository);
        $client->setIntegrationStatus($pullRequest, $status);
    }

    private function getInspectionUrl(Entity\Inspection $inspection): string
    {
        return $this->urlGenerator->generate('inspection_detail', ['id' => $inspection->getId()], UrlGenerator::ABSOLUTE_URL);
    }
}
