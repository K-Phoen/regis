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

namespace Regis\GithubContext\Infrastructure\Worker;

use Regis\GithubContext\Domain\Entity\Inspection;
use Regis\GithubContext\Domain\Repository\PullRequestInspections;
use Regis\GithubContext\Application\Event;
use Regis\Kernel\Event\DomainEventWrapper;
use Swarrot\Broker\Message;
use Swarrot\Processor\ProcessorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

class AnalysisStatusRunner implements ProcessorInterface
{
    private $inspectionsRepo;
    private $dispatcher;

    public function __construct(PullRequestInspections $inspectionsRepo, EventDispatcher $dispatcher)
    {
        $this->inspectionsRepo = $inspectionsRepo;
        $this->dispatcher = $dispatcher;
    }

    public function process(Message $message, array $options)
    {
        $event = json_decode($message->getBody(), true);
        $inspection = $this->inspectionsRepo->find($event['inspection_id']);

        switch ($inspection->getStatus()) {
            case Inspection::STATUS_STARTED:
                $domainEvent = new Event\InspectionStarted($inspection);

                break;
            case Inspection::STATUS_FINISHED:
                $domainEvent = new Event\InspectionFinished($inspection);

                break;
            case Inspection::STATUS_FAILED:
                $domainEvent = new Event\InspectionFailed($inspection);

                break;
            default:
                throw new \LogicException(sprintf('Unknown inspection status: "%s"', $inspection->getStatus()));
        }

        $this->dispatcher->dispatch(get_class($domainEvent), new DomainEventWrapper($domainEvent));
    }
}
