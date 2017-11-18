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

class AddDeployKeyTest extends TestCase
{
    private $bitbucketClientFactory;
    private $repositoriesRepo;
    /** @var CommandHandler\Repository\AddDeployKey */
    private $handler;

    public function setUp()
    {
        $this->bitbucketClientFactory = $this->createMock(BitbucketClientFactory::class);
        $this->repositoriesRepo = $this->createMock(Repositories::class);

        $this->handler = new CommandHandler\Repository\AddDeployKey($this->bitbucketClientFactory, $this->repositoriesRepo);
    }

    public function testItCallsBitbucket()
    {
        $client = $this->createMock(BitbucketClient::class);
        $repository = $this->createMock(Entity\Repository::class);
        $repositoryIdentifier = new RepositoryIdentifier('some-id');

        $repository->method('toIdentifier')->willReturn($repositoryIdentifier);
        $this->repositoriesRepo->method('find')->with('some-id')->willReturn($repository);
        $this->bitbucketClientFactory->method('createForRepository')->with($repository)->willReturn($client);

        $client->expects($this->once())
            ->method('addDeployKey')
            ->with($repositoryIdentifier, $this->anything(), 'key content');

        $this->handler->handle(new Command\Repository\AddDeployKey($repositoryIdentifier, 'key content'));
    }
}
