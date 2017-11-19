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

namespace Regis\GithubContext\Infrastructure\Github;

use Github\Client as VendorClient;
use Psr\Log\LoggerInterface as Logger;
use Regis\GithubContext\Application\Github\Client as GithubClient;
use Regis\GithubContext\Application\Github\ClientFactory as GithubClientFactory;
use Regis\GithubContext\Domain\Entity;

class ClientFactory implements GithubClientFactory
{
    private $client;
    private $logger;

    public function __construct(VendorClient $client, Logger $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    public function createForRepository(Entity\Repository $repository): GithubClient
    {
        $user = $repository->getOwner();

        return $this->createForUser($user);
    }

    public function createForUser(Entity\GithubDetails $user): GithubClient
    {
        return new Client($this->client, $user, $this->logger);
    }
}
