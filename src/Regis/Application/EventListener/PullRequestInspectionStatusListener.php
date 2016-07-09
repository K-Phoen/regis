<?php

declare(strict_types=1);

namespace Regis\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
    const STATUS_CONTEXT = 'regis/pr';

    private $githubFactory;
    private $repositoriesRepo;

    public function __construct(ClientFactory $githubFactory, Repositories $repositoriesRepo)
    {
        $this->githubFactory = $githubFactory;
        $this->repositoriesRepo = $repositoriesRepo;
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

        $this->setIntegrationStatus($domainEvent->getPullRequest(), Client::INTEGRATION_PENDING, 'Inspection scheduled.');
    }

    public function onInspectionStarted(Event\DomainEventWrapper $event)
    {
        /** @var Event\InspectionStarted $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->setIntegrationStatus($domainEvent->getPullRequest(), Client::INTEGRATION_PENDING, 'Inspection started…');
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

        $this->setIntegrationStatus($domainEvent->getPullRequest(), $status, $message);
    }

    public function onInspectionFailed(Event\DomainEventWrapper $event)
    {
        /** @var Event\InspectionFailed $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->setIntegrationStatus($domainEvent->getPullRequest(), Client::INTEGRATION_ERROR, 'Inspection failed.');
    }

    private function setIntegrationStatus(PullRequest $pullRequest, string $status, string $description)
    {
        /** @var Entity\Github\Repository $repository */
        $repository = $this->repositoriesRepo->find($pullRequest->getRepositoryIdentifier());
        $client = $this->githubFactory->createForRepository($repository);

        $client->setIntegrationStatus($pullRequest, $status, $description, self::STATUS_CONTEXT);
    }
}
