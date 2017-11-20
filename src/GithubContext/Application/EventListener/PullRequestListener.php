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

use League\Tactician\CommandBus;
use Regis\Kernel\Event\DomainEventWrapper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Regis\GithubContext\Application\Event;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\Inspection\ViolationsCache;

class PullRequestListener implements EventSubscriberInterface
{
    private $commandBus;
    private $violationsCache;

    public function __construct(CommandBus $commandBus, ViolationsCache $violationsCache)
    {
        $this->violationsCache = $violationsCache;
        $this->commandBus = $commandBus;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Event\PullRequestOpened::class => 'onPullRequestUpdated',
            Event\PullRequestSynced::class => 'onPullRequestUpdated',
            Event\PullRequestClosed::class => 'onPullRequestClosed',
        ];
    }

    public function onPullRequestUpdated(DomainEventWrapper $event): void
    {
        /** @var Event\PullRequestOpened|Event\PullRequestSynced $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $command = new Command\Inspection\SchedulePullRequest($domainEvent->getPullRequest());
        $this->commandBus->handle($command);
    }

    public function onPullRequestClosed(DomainEventWrapper $event): void
    {
        /** @var Event\PullRequestClosed $domainEvent */
        $domainEvent = $event->getDomainEvent();

        // TODO should be in a command
        $this->violationsCache->clear($domainEvent->getPullRequest());
    }
}
