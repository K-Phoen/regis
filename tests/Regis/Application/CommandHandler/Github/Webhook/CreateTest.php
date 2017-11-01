<?php

namespace Tests\Regis\Application\CommandHandler\Github\Webhook;

use PHPUnit\Framework\TestCase;
use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Application\Github\Client as GithubClient;
use Regis\Application\Github\ClientFactory as GithubClientFactory;
use Regis\Domain\Entity;
use Regis\Domain\Repository\Repositories;

class CreateTest extends TestCase
{
    private $githubClientFactory;
    private $repositoriesRepo;
    /** @var CommandHandler\Github\Webhook\CreateWebhook */
    private $handler;

    public function setUp()
    {
        $this->githubClientFactory = $this->getMockBuilder(GithubClientFactory::class)->disableOriginalConstructor()->getMock();
        $this->repositoriesRepo = $this->getMockBuilder(Repositories::class)->getMock();

        $this->handler = new CommandHandler\Github\Webhook\CreateWebhook($this->githubClientFactory, $this->repositoriesRepo);
    }

    public function testItCallsGithub()
    {
        $command = new Command\Github\Webhook\CreateWebhook('K-Phoen', 'test', 'http://callback.url');
        $client = $this->getMockBuilder(GithubClient::class)->getMock();

        $repository = $this->getMockBuilder(Entity\Github\Repository::class)->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())
            ->method('getSharedSecret')
            ->will($this->returnValue('shared secret'));

        $this->repositoriesRepo->expects($this->once())
            ->method('find')
            ->with('K-Phoen/test')
            ->will($this->returnValue($repository));

        $this->githubClientFactory->expects($this->once())
            ->method('createForRepository')
            ->with($repository)
            ->will($this->returnValue($client));

        $client->expects($this->once())
            ->method('createWebhook')
            ->with('K-Phoen', 'test', 'http://callback.url', 'shared secret');

        $this->handler->handle($command);
    }
}
