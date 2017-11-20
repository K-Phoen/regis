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

namespace Regis\AppContext\Application\Remote;

use League\Tactician\CommandBus;
use Regis\AppContext\Domain\Entity;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Model\RepositoryIdentifier;

class GithubActions implements Actions
{
    private $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    public function createWebhook(Entity\Repository $repository, string $hookUrl): void
    {
        $command = new Command\Repository\CreateWebhook(
            RepositoryIdentifier::fromFullName($repository->getIdentifier()),
            $hookUrl
        );

        $this->bus->handle($command);
    }
}
