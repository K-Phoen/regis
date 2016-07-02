<?php

namespace Tests\Regis\Infrastructure\Github;

use Github\Api;
use Github\Client as VendorClient;
use Psr\Log\LoggerInterface;

use Regis\Domain\Model\Github\PullRequest;
use Regis\Domain\Model\Github\ReviewComment;
use Regis\Infrastructure\Github\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    const API_TOKEN = 'some api token';

    private $vendorClient;
    private $prApi;
    private $repoApi;
    private $prCommentsApi;
    private $statusesApi;
    private $keysApi;
    private $hooksApi;

    private $logger;
    /** @var Client */
    private $client;

    public function setUp()
    {
        $this->vendorClient = $this->getMockBuilder(VendorClient::class)
            ->setMethods(['authenticate', 'repo', 'pullRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $this->prApi = $this->getMockBuilder(Api\PullRequest::class)->disableOriginalConstructor()->getMock();
        $this->repoApi = $this->getMockBuilder(Api\Repo::class)->disableOriginalConstructor()->getMock();
        $this->prCommentsApi = $this->getMockBuilder(Api\PullRequest\Comments::class)->disableOriginalConstructor()->getMock();
        $this->statusesApi = $this->getMockBuilder(Api\Repository\Statuses::class)->disableOriginalConstructor()->getMock();
        $this->keysApi = $this->getMockBuilder(Api\Repository\DeployKeys::class)->disableOriginalConstructor()->getMock();
        $this->hooksApi = $this->getMockBuilder(Api\Repository\Hooks::class)->disableOriginalConstructor()->getMock();

        $this->vendorClient->expects($this->any())
            ->method('pullRequest')
            ->will($this->returnValue($this->prApi));
        $this->vendorClient->expects($this->any())
            ->method('repo')
            ->will($this->returnValue($this->repoApi));

        $this->prApi->expects($this->any())
            ->method('comments')
            ->will($this->returnValue($this->prCommentsApi));
        $this->repoApi->expects($this->any())
            ->method('statuses')
            ->will($this->returnValue($this->statusesApi));
        $this->repoApi->expects($this->any())
            ->method('keys')
            ->will($this->returnValue($this->keysApi));
        $this->repoApi->expects($this->any())
            ->method('hooks')
            ->will($this->returnValue($this->hooksApi));

        $this->client = new Client($this->vendorClient, self::API_TOKEN, $this->logger);
    }

    public function testSetIntegrationStatus()
    {
        $pr = $this->getMockBuilder(PullRequest::class)->disableOriginalConstructor()->getMock();

        $this->vendorClient->expects($this->once())->method('authenticate');
        $this->statusesApi->expects($this->once())->method('create');

        $this->client->setIntegrationStatus($pr, 'state', 'description', 'context');
    }

    public function testAddDeployKey()
    {
        $this->vendorClient->expects($this->once())->method('authenticate');
        $this->keysApi->expects($this->once())->method('create');

        $this->client->addDeployKey('owner', 'repo', 'key content', 'type', Client::READONLY_KEY);
    }

    public function testCreateWebhook()
    {
        $this->vendorClient->expects($this->once())->method('authenticate');
        $this->hooksApi->expects($this->once())->method('create');

        $this->client->createWebhook('owner', 'repo', 'url', 'secret');
    }

    public function testSendComment()
    {
        $pr = $this->getMockBuilder(PullRequest::class)->disableOriginalConstructor()->getMock();
        $reviewComment = $this->getMockBuilder(ReviewComment::class)->disableOriginalConstructor()->getMock();

        $this->vendorClient->expects($this->once())->method('authenticate');
        $this->prCommentsApi->expects($this->once())->method('create');

        $this->client->sendComment($pr, $reviewComment);
    }
}
