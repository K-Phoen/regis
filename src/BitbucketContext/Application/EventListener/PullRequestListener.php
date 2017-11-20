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

use League\Tactician\CommandBus;
use Regis\Kernel\Event\DomainEventWrapper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Regis\BitbucketContext\Application\Event;
use Regis\BitbucketContext\Application\Command;

class PullRequestListener implements EventSubscriberInterface
{
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Event\PullRequestOpened::class => 'onPullRequestUpdated',
            Event\PullRequestUpdated::class => 'onPullRequestUpdated',
        ];
    }

    public function onPullRequestUpdated(DomainEventWrapper $event): void
    {
        /** @var Event\PullRequestOpened|Event\PullRequestUpdated $domainEvent */
        $domainEvent = $event->getDomainEvent();

        $command = new Command\Inspection\SchedulePullRequest($domainEvent->getPullRequest());
        $this->commandBus->handle($command);
    }
}
