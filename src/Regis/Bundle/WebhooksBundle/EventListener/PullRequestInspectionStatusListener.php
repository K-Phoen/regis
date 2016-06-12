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
            Event::INSPECTION_FAILED => 'onInspectionFailed',
        ];
    }

    public function onPullRequestUpdated(DomainEventWrapper $event)
    {
        /** @var Event\PullRequestOpened|Event\PullRequestSynced $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->setIntegrationStatus($domainEvent->getPullRequest(), Client::INTEGRATION_PENDING, 'Inspection scheduled.');
    }

    public function onInspectionStated(DomainEventWrapper $event)
    {
        /** @var Event\InspectionStarted $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->setIntegrationStatus($domainEvent->getPullRequest(), Client::INTEGRATION_PENDING, 'Inspection started…');
    }

    public function onInspectionFinished(DomainEventWrapper $event)
    {
        /** @var Event\InspectionFinished $domainEvent */
        $domainEvent = $event->getDomainEvent();
        $report = $domainEvent->getReportSummary();

        if ($report->errorsCount() > 0 || $report->warningsCount() > 0) {
            list($status, $message) = [Client::INTEGRATION_FAILURE, sprintf('Inspection with %d error(s) and %d warning(s).', $report->errorsCount(), $report->warningsCount())];
        } else {
            list($status, $message) = [Client::INTEGRATION_SUCCESS, 'Inspection successfull.'];
        }

        $this->setIntegrationStatus($domainEvent->getPullRequest(), $status, $message);
    }

    public function onInspectionFailed(DomainEventWrapper $event)
    {
        /** @var Event\InspectionFailed $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $this->setIntegrationStatus($domainEvent->getPullRequest(), Client::INTEGRATION_FAILURE, 'Inspection failed.');
    }

    private function setIntegrationStatus(PullRequest $pullRequest, string $status, string $description)
    {
        $this->github->setIntegrationStatus($pullRequest, $status, $description, self::STATUS_CONTEXT);
    }
}