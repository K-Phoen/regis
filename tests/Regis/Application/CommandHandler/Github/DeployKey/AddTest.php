<?php

namespace Tests\Regis\Application\CommandHandler\Github\DeployKey;

use PHPUnit\Framework\TestCase;
use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Application\Github\Client as GithubClient;
use Regis\Application\Github\ClientFactory as GithubClientFactory;
use Regis\Domain\Entity;
use Regis\Domain\Repository\Repositories;

class AddTest extends TestCase
{
    private $githubClientFactory;
    private $repositoriesRepo;
    /** @var CommandHandler\Github\Webhook\CreateWebhook */
    private $handler;

    public function setUp()
    {
        $this->githubClientFactory = $this->getMockBuilder(GithubClientFactory::class)->disableOriginalConstructor()->getMock();
        $this->repositoriesRepo = $this->getMockBuilder(Repositories::class)->getMock();

        $this->handler = new CommandHandler\Github\DeployKey\AddDeployKey($this->githubClientFactory, $this->repositoriesRepo);
    }

    public function testItCallsGithub()
    {
        $command = new Command\Github\DeployKey\AddDeployKey('K-Phoen', 'test', 'key content');
        $client = $this->getMockBuilder(GithubClient::class)->getMock();

        $repository = $this->getMockBuilder(Entity\Github\Repository::class)->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())
            ->method('getOwnerUsername')
            ->will($this->returnValue('K-Phoen'));
        $repository->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('test'));

        $this->repositoriesRepo->expects($this->once())
            ->method('find')
            ->with('K-Phoen/test')
            ->will($this->returnValue($repository));

        $this->githubClientFactory->expects($this->once())
            ->method('createForRepository')
            ->with($repository)
            ->will($this->returnValue($client));

        $client->expects($this->once())
            ->method('addDeployKey')
            ->with('K-Phoen', 'test', $this->anything(), 'key content', GithubClient::READONLY_KEY);

        $this->handler->handle($command);
    }
}
