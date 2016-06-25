<?php

namespace Tests\Regis\Application\CommandHandler\Webhook;

use Regis\Application\Repository\Repositories;
use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Application\Entity;
use Regis\Github\Client as GithubClient;

class CreateTest extends \PHPUnit_Framework_TestCase
{
    private $githubClient;
    private $repositoriesRepo;
    /** @var CommandHandler\Webhook\Create */
    private $handler;

    public function setUp()
    {
        $this->githubClient = $this->getMockBuilder(GithubClient::class)->disableOriginalConstructor()->getMock();
        $this->repositoriesRepo = $this->getMockBuilder(Repositories::class)->getMock();

        $this->handler = new CommandHandler\Webhook\Create($this->githubClient, $this->repositoriesRepo);
    }

    public function testItCallsGithub()
    {
        $command = new Command\Webhook\Create('K-Phoen', 'test', 'http://callback.url');
        $repository = $this->getMockBuilder(Entity\Repository::class)->getMock();
        $repository->expects($this->once())
            ->method('getSharedSecret')
            ->will($this->returnValue('shared secret'));

        $this->repositoriesRepo->expects($this->once())
            ->method('find')
            ->with('K-Phoen/test')
            ->will($this->returnValue($repository));

        $this->githubClient->expects($this->once())
            ->method('createWebhook')
            ->with('K-Phoen', 'test', 'http://callback.url', 'shared secret');

        $this->handler->handle($command);
    }
}
