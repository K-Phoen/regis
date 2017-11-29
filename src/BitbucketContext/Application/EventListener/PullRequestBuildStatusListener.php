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

namespace Regis\BitbucketContext\Application\EventListener;

use Regis\Kernel\Event\DomainEventWrapper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as UrlGenerator;
use Regis\BitbucketContext\Application\Event;
use Regis\BitbucketContext\Application\Bitbucket\ClientFactory;
use Regis\BitbucketContext\Domain\Entity;
use Regis\BitbucketContext\Application\Bitbucket\BuildStatus;

class PullRequestBuildStatusListener implements EventSubscriberInterface
{
    private $bitbucketFactory;
    private $urlGenerator;

    public function __construct(ClientFactory $bitbucketFactory, UrlGenerator $urlGenerator)
    {
        $this->bitbucketFactory = $bitbucketFactory;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Event\InspectionStarted::class => 'onInspectionStarted',
            Event\InspectionFinished::class => 'onInspectionFinished',
            Event\InspectionFailed::class => 'onInspectionFailed',
        ];
    }

    public function onInspectionStarted(DomainEventWrapper $event): void
    {
        /** @var Event\InspectionStarted $domainEvent */
        $domainEvent = $event->getDomainEvent();
        $inspection = $domainEvent->getInspection();

        $this->setBuildStatus(
            $inspection->getRepository(),
            BuildStatus::inProgress($inspection->getHead(), 'Inspection started…', $this->getInspectionUrl($inspection)),
            $inspection->getHead()
        );
    }

    public function onInspectionFinished(DomainEventWrapper $event): void
    {
        /** @var Event\InspectionFinished $domainEvent */
        $domainEvent = $event->getDomainEvent();
        $inspection = $domainEvent->getInspection();

        if (!$inspection->hasReport()) {
            $this->setBuildStatus(
                $inspection->getRepository(),
                BuildStatus::failed($inspection->getHead(), 'Internal error.', $this->getInspectionUrl($inspection)),
                $inspection->getHead()
            );

            return;
        }

        $report = $inspection->getReport();

        if ($report->hasErrors() || $report->hasWarnings()) {
            [$state, $message] = [BuildStatus::STATE_FAILED, sprintf('Inspection finished with %d error(s) and %d warning(s).', $report->errorsCount(), $report->warningsCount())];
        } else {
            [$state, $message] = [BuildStatus::STATE_SUCCESSFUL, 'Inspection successful.'];
        }

        $this->setBuildStatus(
            $inspection->getRepository(),
            new BuildStatus($inspection->getHead(), $state, $message, $this->getInspectionUrl($inspection)),
            $inspection->getHead()
        );
    }

    public function onInspectionFailed(DomainEventWrapper $event): void
    {
        /** @var Event\InspectionFailed $domainEvent */
        $domainEvent = $event->getDomainEvent();
        $inspection = $domainEvent->getInspection();

        $this->setBuildStatus(
            $inspection->getRepository(),
            BuildStatus::failed($inspection->getHead(), 'Inspection failed.', $this->getInspectionUrl($inspection)),
            $inspection->getHead()
        );
    }

    private function setBuildStatus(Entity\Repository $repository, BuildStatus $status, string $head): void
    {
        if (!$repository->isInspectionEnabled() || $repository->isFlightModeEnabled()) {
            return;
        }

        $client = $this->bitbucketFactory->createForRepository($repository);
        $client->setBuildStatus($repository->toIdentifier(), $status, $head);
    }

    private function getInspectionUrl(Entity\Inspection $inspection): string
    {
        return $this->urlGenerator->generate('inspection_detail', ['id' => $inspection->getId()], UrlGenerator::ABSOLUTE_URL);
    }
}
