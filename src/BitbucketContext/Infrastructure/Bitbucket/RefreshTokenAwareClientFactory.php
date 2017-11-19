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

use KnpU\OAuth2ClientBundle\Client\Provider\BitbucketClient as OauthBitbucketClient;
use League\Tactician\CommandBus;
use Psr\Log\LoggerInterface as Logger;
use Regis\BitbucketContext\Application\Bitbucket\Client as BitbucketClient;
use Regis\BitbucketContext\Application\Bitbucket\ClientFactory as BitbucketClientFactory;
use Regis\BitbucketContext\Domain\Entity;
use Regis\BitbucketContext\Application\Command;

class RefreshTokenAwareClientFactory implements BitbucketClientFactory
{
    private $bus;
    private $decoratedFactory;
    private $oauthBitbucketClient;
    private $logger;

    public function __construct(CommandBus $bus, BitbucketClientFactory $decoratedFactory, OauthBitbucketClient $oauthBitbucketClient, Logger $logger)
    {
        $this->bus = $bus;
        $this->oauthBitbucketClient = $oauthBitbucketClient;
        $this->decoratedFactory = $decoratedFactory;
        $this->logger = $logger;
    }

    public function createForRepository(Entity\Repository $repository): BitbucketClient
    {
        $user = $repository->getOwner();

        return $this->createForUser($user);
    }

    public function createForUser(Entity\BitbucketDetails $user): BitbucketClient
    {
        if ($user->isAccessTokenObsolete(new \DateTimeImmutable())) {
            $this->refreshToken($user);
        }

        return $this->decoratedFactory->createForUser($user);
    }

    private function refreshToken(Entity\BitbucketDetails $user)
    {
        $this->logger->info('Refreshing token for user {user}', [
            'user' => $user->getId(),
            'access_toke_expiration' => $user->getAccessTokenExpiration(),
        ]);

        $provider = $this->oauthBitbucketClient->getOAuth2Provider();

        $newToken = $provider->getAccessToken('refresh_token', [
            'refresh_token' => $user->getRefreshToken(),
        ]);

        $command = new Command\User\CreateOrUpdateUser(
            $user->getUsername(),
            $user->getRemoteId(),
            $newToken->getToken(),
            $newToken->getRefreshToken(),
            (new \DateTimeImmutable())->setTimestamp($newToken->getExpires())
        );

        $this->bus->handle($command);
    }
}
