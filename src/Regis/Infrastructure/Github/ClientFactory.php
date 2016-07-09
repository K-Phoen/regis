<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Github;

use Psr\Log\LoggerInterface as Logger;
use Regis\Application\Github\Client as GithubClient;
use Regis\Application\Github\ClientFactory as GithubClientFactory;
use Regis\Domain\Entity;

class ClientFactory implements GithubClientFactory
{
    private $client;
    private $logger;

    public function __construct(\Github\Client $client, Logger $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    public function createForRepository(Entity\Github\Repository $repository): GithubClient
    {
        return $this->createForUser($repository->getOwner());
    }

    public function createForUser(Entity\User $user): GithubClient
    {
        return new Client($this->client, $user->getGithubAccessToken(), $this->logger);
    }
}
