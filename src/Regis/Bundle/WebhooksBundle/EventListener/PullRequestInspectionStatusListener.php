<?php

declare(strict_types=1);

namespace Regis\Bundle\WebhooksBundle\EventListener;

use Regis\Application\Model\Github\PullRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Regis\Bundle\WebhooksBundle\Event\DomainEventWrapper;
use Regis\Application\Event;
use Regis\Github\Client;

class PullRequestInspectionStatusListener implements EventSubscriberInterface
{
    const STATUS_CONTEXT = 'regis/pr';

    private $github;

    public function __construct(Client $github)
    {
        $this->github = $github;
    }

    public static function getSubscribedEvents()
    {
        return [
            Event::PULL_REQUEST_OPENED => 'onPullRequestUpdated',
            Event::PULL_REQUEST_SYNCED => 'onPullRequestUpdated',
            Event::INSPECTION_STARTED => 'onInspectionStated',
            Event::INSPECTION_FINISHED => 'onInspectionFinished',
        ];
    }

    public function onPullRequestUpdated(DomainEventWrapper $event)
    {
        /** @var Event\PullRequestOpened|Event\PullRequestSynced $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->createIntegrationStatus($domainEvent->getPullRequest(), Client::INTEGRATION_PENDING, 'Inspection scheduled.');
    }

    public function onInspectionStated(DomainEventWrapper $event)
    {
        /** @var Event\InspectionStarted $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->createIntegrationStatus($domainEvent->getPullRequest(), Client::INTEGRATION_PENDING, 'Inspection started…');
    }

    public function onInspectionFinished(DomainEventWrapper $event)
    {
        /** @var Event\InspectionFinished $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->createIntegrationStatus($domainEvent->getPullRequest(), Client::INTEGRATION_SUCCESS, 'Inspection finished.');
    }

    private function createIntegrationStatus(PullRequest $pullRequest, string $status, string $description)
    {
        $this->github->createIntegrationStatus($pullRequest, $status, $description, self::STATUS_CONTEXT);
    }
}