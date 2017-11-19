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

namespace Regis\BitbucketContext\Infrastructure\Bitbucket;

use Bitbucket\API\Api;
use Bitbucket\API\Http\ClientInterface as HttpClient;
use Bitbucket\API\Http\Listener\OAuth2Listener;
use Psr\Log\LoggerInterface as Logger;
use Regis\BitbucketContext\Application\Bitbucket\Client as BitbucketClient;
use Regis\BitbucketContext\Application\Bitbucket\ClientFactory as BitbucketClientFactory;
use Regis\BitbucketContext\Domain\Entity;

class ClientFactory implements BitbucketClientFactory
{
    private $httpClient;
    private $logger;

    public function __construct(Logger $logger, HttpClient $httpClient = null)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    public function createForRepository(Entity\Repository $repository): BitbucketClient
    {
        $user = $repository->getOwner();

        return $this->createForUser($user);
    }

    public function createForUser(Entity\BitbucketDetails $user): BitbucketClient
    {
        $bitbucket = new Api([], $this->httpClient);
        $bitbucket->getClient()->addListener(
            new OAuth2Listener(
                ['access_token' => $user->getAccessToken()]
            )
        );

        return new Client($bitbucket, $user, $this->logger);
    }
}
