<?php

declare(strict_types=1);

namespace Tests\Regis\BitbucketContext\Application\CommandHandler\Repository;

use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Application\CommandHandler;
use Regis\BitbucketContext\Application\Bitbucket\Client as BitbucketClient;
use Regis\BitbucketContext\Application\Bitbucket\ClientFactory as BitbucketClientFactory;
use Regis\BitbucketContext\Domain\Entity;
use Regis\BitbucketContext\Domain\Model\RepositoryIdentifier;
use Regis\BitbucketContext\Domain\Repository\Repositories;

class CreateWebhookTest extends TestCase
{
    private $bitbucketClientFactory;
    private $repositoriesRepo;
    /** @var CommandHandler\Repository\CreateWebhook */
    private $handler;

    public function setUp()
    {
        $this->bitbucketClientFactory = $this->createMock(BitbucketClientFactory::class);
        $this->repositoriesRepo = $this->createMock(Repositories::class);

        $this->handler = new CommandHandler\Repository\CreateWebhook($this->bitbucketClientFactory, $this->repositoriesRepo);
    }

    public function testItCallsBitbucket()
    {
        $client = $this->createMock(BitbucketClient::class);
        $repository = $this->createMock(Entity\Repository::class);
        $repositoryIdentifier = new RepositoryIdentifier('repo-id');

        $repository->method('toIdentifier')->willReturn($repositoryIdentifier);

        $this->repositoriesRepo->method('find')->with('repo-id')->willReturn($repository);
        $this->bitbucketClientFactory->method('createForRepository')->with($repository)->willReturn($client);

        $client->expects($this->once())
            ->method('createWebhook')
            ->with($repositoryIdentifier, 'http://callback.url');

        $this->handler->handle(new Command\Repository\CreateWebhook($repositoryIdentifier, 'http://callback.url'));
    }
}
