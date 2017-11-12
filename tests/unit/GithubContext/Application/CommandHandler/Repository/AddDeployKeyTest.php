<?php

namespace Tests\Regis\GithubContext\Application\CommandHandler\Repository;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\CommandHandler;
use Regis\GithubContext\Application\Github\Client as GithubClient;
use Regis\GithubContext\Application\Github\ClientFactory as GithubClientFactory;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Model\RepositoryIdentifier;
use Regis\GithubContext\Domain\Repository\Repositories;

class AddDeployKeyTest extends TestCase
{
    private $githubClientFactory;
    private $repositoriesRepo;
    /** @var CommandHandler\Repository\AddDeployKey */
    private $handler;

    public function setUp()
    {
        $this->githubClientFactory = $this->createMock(GithubClientFactory::class);
        $this->repositoriesRepo = $this->createMock(Repositories::class);

        $this->handler = new CommandHandler\Repository\AddDeployKey($this->githubClientFactory, $this->repositoriesRepo);
    }

    public function testItCallsGithub()
    {
        $client = $this->createMock(GithubClient::class);
        $repository = $this->createMock(Entity\Repository::class);
        $repositoryIdentifier = RepositoryIdentifier::fromFullName('K-Phoen/test');

        $repository->method('toIdentifier')->willReturn($repositoryIdentifier);

        $this->repositoriesRepo
            ->method('find')
            ->with($repositoryIdentifier->getIdentifier())
            ->willReturn($repository);

        $this->githubClientFactory
            ->method('createForRepository')
            ->with($repository)
            ->willReturn($client);

        $client->expects($this->once())
            ->method('addDeployKey')
            ->with($repositoryIdentifier, $this->anything(), 'key content', GithubClient::READONLY_KEY);

        $this->handler->handle(new Command\Repository\AddDeployKey('K-Phoen', 'test', 'key content'));
    }
}
