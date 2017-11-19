<?php

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
            $user->getId(),
            $newToken->getToken(),
            $newToken->getRefreshToken(),
            (new \DateTimeImmutable())->setTimestamp($newToken->getExpires())
        );

        $this->bus->handle($command);
    }
}
