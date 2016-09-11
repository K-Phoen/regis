<?php

namespace Tests\Regis\Infrastructure\Github;

use Github\Api;
use Github\Client as VendorClient;
use Github\HttpClient\HttpClient;
use Guzzle\Http\Message\Response;
use Psr\Log\LoggerInterface;

use Regis\Application\Github\IntegrationStatus;
use Regis\Domain\Entity\User;
use Regis\Domain\Model\Github\PullRequest;
use Regis\Domain\Model\Github\Repository;
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
    private $currentUserApi;

    private $logger;
    private $user;
    /** @var Client */
    private $client;

    public function setUp()
    {
        $this->vendorClient = $this->getMockBuilder(VendorClient::class)
            ->setMethods(['authenticate', 'repo', 'pullRequest', 'currentUser', 'getHttpClient'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $this->user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();

        $this->user->expects($this->any())
            ->method('getGithubAccessToken')
            ->will($this->returnValue(self::API_TOKEN));

        $this->prApi = $this->getMockBuilder(Api\PullRequest::class)->disableOriginalConstructor()->getMock();
        $this->repoApi = $this->getMockBuilder(Api\Repo::class)->disableOriginalConstructor()->getMock();
        $this->prCommentsApi = $this->getMockBuilder(Api\PullRequest\Comments::class)->disableOriginalConstructor()->getMock();
        $this->statusesApi = $this->getMockBuilder(Api\Repository\Statuses::class)->disableOriginalConstructor()->getMock();
        $this->keysApi = $this->getMockBuilder(Api\Repository\DeployKeys::class)->disableOriginalConstructor()->getMock();
        $this->hooksApi = $this->getMockBuilder(Api\Repository\Hooks::class)->disableOriginalConstructor()->getMock();
        $this->currentUserApi= $this->getMockBuilder(Api\CurrentUser::class)->disableOriginalConstructor()->getMock();

        $this->vendorClient->expects($this->any())
            ->method('pullRequest')
            ->will($this->returnValue($this->prApi));
        $this->vendorClient->expects($this->any())
            ->method('repo')
            ->will($this->returnValue($this->repoApi));
        $this->vendorClient->expects($this->any())
            ->method('currentUser')
            ->will($this->returnValue($this->currentUserApi));

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

        $this->client = new Client($this->vendorClient, $this->user, $this->logger);
    }

    public function testSetIntegrationStatus()
    {
        $status = new IntegrationStatus('some state', 'some description');
        $pr = $this->getMockBuilder(PullRequest::class)->disableOriginalConstructor()->getMock();

        $this->vendorClient->expects($this->once())->method('authenticate');
        $this->statusesApi->expects($this->once())->method('create');

        $this->client->setIntegrationStatus($pr, $status);
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

    public function testListRepositories()
    {
        $httpClient = $this->getMockBuilder(HttpClient::class)->disableOriginalConstructor()->getMock();
        $httpResponse = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();

        $this->vendorClient->expects($this->once())
            ->method('getHttpClient')
            ->will($this->returnValue($httpClient));

        $httpClient->expects($this->once())
            ->method('getLastResponse')
            ->will($this->returnValue($httpResponse));

        $this->vendorClient->expects($this->once())->method('authenticate');

        $this->currentUserApi->expects($this->once())
            ->method('repositories')
            ->will($this->returnValue([
                [
                    'full_name' => 'first/repo',
                    'html_url' => 'first/repo → html_url',
                    'ssh_url' => 'first/repo → ssh_url',
                    'private' => false,
                ],
                [
                    'full_name' => 'second/repo',
                    'html_url' => 'second/repo → html_url',
                    'clone_url' => 'second/repo → clone_url',
                    'private' => true,
                ],
            ]));

        $repositories = iterator_to_array($this->client->listRepositories());

        $this->assertCount(2, $repositories);

        $this->assertInstanceOf(Repository::class, $repositories[0]);
        $this->assertEquals('first/repo', $repositories[0]->getIdentifier());
        $this->assertEquals('first/repo → html_url', $repositories[0]->getPublicUrl());
        $this->assertEquals('first/repo → ssh_url', $repositories[0]->getCloneUrl());

        $this->assertInstanceOf(Repository::class, $repositories[1]);
        $this->assertEquals('second/repo', $repositories[1]->getIdentifier());
        $this->assertEquals('second/repo → html_url', $repositories[1]->getPublicUrl());
        $this->assertEquals('second/repo → clone_url', $repositories[1]->getCloneUrl());
    }
}
