<?php

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
