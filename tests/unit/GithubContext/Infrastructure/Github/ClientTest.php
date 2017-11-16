<?php

namespace Tests\Regis\GithubContext\Infrastructure\Github;

use PHPUnit\Framework\TestCase;
use Github\Api;
use Github\Client as VendorClient;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

use Regis\GithubContext\Application\Github\IntegrationStatus;
use Regis\GithubContext\Domain\Entity\GithubDetails;
use Regis\GithubContext\Domain\Model\PullRequest;
use Regis\GithubContext\Domain\Model\Repository;
use Regis\GithubContext\Domain\Model\RepositoryIdentifier;
use Regis\GithubContext\Domain\Model\ReviewComment;
use Regis\GithubContext\Infrastructure\Github\Client;

class ClientTest extends TestCase
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
            ->setMethods(['authenticate', 'repo', 'pullRequest', 'currentUser', 'getLastResponse'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->user = $this->createMock(GithubDetails::class);

        $this->user->method('getAccessToken')->willReturn(self::API_TOKEN);

        $this->prApi = $this->createMock(Api\PullRequest::class);
        $this->repoApi = $this->createMock(Api\Repo::class);
        $this->prCommentsApi = $this->createMock(Api\PullRequest\Comments::class);
        $this->statusesApi = $this->createMock(Api\Repository\Statuses::class);
        $this->keysApi = $this->createMock(Api\Repository\DeployKeys::class);
        $this->hooksApi = $this->createMock(Api\Repository\Hooks::class);
        $this->currentUserApi = $this->createMock(Api\CurrentUser::class);

        $this->vendorClient->method('pullRequest')->willReturn($this->prApi);
        $this->vendorClient->method('repo')->willReturn($this->repoApi);
        $this->vendorClient->method('currentUser')->willReturn($this->currentUserApi);

        $this->prApi->method('comments')->willReturn($this->prCommentsApi);

        $this->repoApi->method('statuses')->willReturn($this->statusesApi);
        $this->repoApi->method('keys')->willReturn($this->keysApi);
        $this->repoApi->method('hooks')->willReturn($this->hooksApi);

        $this->client = new Client($this->vendorClient, $this->user, $this->logger);
    }

    public function testSetIntegrationStatus()
    {
        $status = new IntegrationStatus('some state', 'some description');

        $this->vendorClient->expects($this->once())->method('authenticate');

        $this->statusesApi->expects($this->once())
            ->method('create')
            ->with('K-Phoen', 'test', 'head commit', [
                'state' => $status->getState(),
                'description' => $status->getDescription(),
                'context' => IntegrationStatus::STATUS_CONTEXT,
            ]);

        $this->client->setIntegrationStatus(RepositoryIdentifier::fromFullName('K-Phoen/test'), 'head commit', $status);
    }

    public function testSetIntegrationStatusWithATargetUrl()
    {
        $status = new IntegrationStatus('some state', 'some description', 'http://foo/bar');

        $this->vendorClient->expects($this->once())->method('authenticate');
        $this->statusesApi->expects($this->once())
            ->method('create')
            ->with('K-Phoen', 'test', 'head commit', [
                'state' => $status->getState(),
                'description' => $status->getDescription(),
                'context' => IntegrationStatus::STATUS_CONTEXT,
                'target_url' => $status->getTargetUrl(),
            ]);

        $this->client->setIntegrationStatus(RepositoryIdentifier::fromFullName('K-Phoen/test'), 'head commit', $status);
    }

    public function testAddDeployKey()
    {
        $this->vendorClient->expects($this->once())->method('authenticate');

        $this->keysApi->expects($this->once())
            ->method('create')
            ->with('K-Phoen', 'test', [
                'title' => 'key title',
                'key' => 'key content',
                'read_only' => true,
            ]);

        $this->client->addDeployKey(RepositoryIdentifier::fromFullName('K-Phoen/test'), 'key title', 'key content', Client::READONLY_KEY);
    }

    public function testCreateWebhook()
    {
        $this->vendorClient->expects($this->once())->method('authenticate');

        $this->hooksApi->expects($this->once())
            ->method('create')
            ->with('K-Phoen', 'test', [
                'name' => 'web',
                'config' => [
                    'url' => 'some url',
                    'content_type' => 'json',
                    'secret' => 'some secret',
                ],
                'events' => ['*'],
                'active' => true,
            ]);

        $this->client->createWebhook(RepositoryIdentifier::fromFullName('K-Phoen/test'), 'some url', 'some secret');
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
        $httpResponse = $this->createMock(ResponseInterface::class);
        $this->vendorClient->method('getLastResponse')->willReturn($httpResponse);

        $this->vendorClient->expects($this->once())->method('authenticate');

        $this->currentUserApi->expects($this->once())
            ->method('repositories')
            ->willReturn([
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
            ]);

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
