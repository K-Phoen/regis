<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Github;

use Github\Client as VendorClient;
use Psr\Log\LoggerInterface as Logger;
use Regis\Application\Github\Client as GithubClient;
use Regis\Application\Github\ClientFactory as GithubClientFactory;
use Regis\Domain\Entity;

class ClientFactory implements GithubClientFactory
{
    private $client;
    private $logger;

    public function __construct(VendorClient $client, Logger $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    public function createForRepository(Entity\Github\Repository $repository): GithubClient
    {
        $user = $repository->getOwner();

        return $this->createForUser($user);
    }

    public function createForUser(Entity\User $user): GithubClient
    {
        return new Client($this->client, $user, $this->logger);
    }
}
