<?php

declare(strict_types=1);

namespace Tests\Regis\BitbucketContext\Infrastructure\Github;

use Buzz\Message\MessageInterface;
use PHPUnit\Framework\TestCase;
use Bitbucket\API\Api as VendorClient;
use Psr\Log\LoggerInterface;
use Regis\BitbucketContext\Application\Bitbucket\BuildStatus;
use Regis\BitbucketContext\Domain\Entity\BitbucketDetails;
use Regis\BitbucketContext\Domain\Model\RepositoryIdentifier;
use Regis\BitbucketContext\Domain\Model\PullRequest;
use Regis\BitbucketContext\Infrastructure\Bitbucket\Client;
use Bitbucket\API\Repositories;

class ClientTest extends TestCase
{
    const API_TOKEN = 'some api token';
    const USERNAME = 'some username';
    const REPOSITORY_ID = 'some-repo-id';

    private $vendorClient;

    private $logger;
    private $user;
    /** @var Client */
    private $client;

    public function setUp()
    {
        $this->vendorClient = $this->createMock(VendorClient::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->user = $this->createMock(BitbucketDetails::class);

        $this->user->method('getUsername')->willReturn(self::USERNAME);
        $this->user->method('getAccessToken')->willReturn(self::API_TOKEN);

        $this->client = new Client($this->vendorClient, $this->user, $this->logger);
    }

    public function testSetBuildStatus()
    {
        $revision = 'revision sha';
        $status = BuildStatus::inProgress('some-state-key', 'some description', 'some-url');

        $statusesApi = $this->createMock(Repositories\Commits\BuildStatuses::class);

        $this->vendorClient->method('api')->with('Repositories\\Commits\\BuildStatuses')->willReturn($statusesApi);

        $statusesApi->expects($this->once())
            ->method('create')
            ->with(self::USERNAME, self::REPOSITORY_ID, $revision, [
                'state' => BuildStatus::STATE_INPROGRESS,
                'key' => sha1('regis-some-state-key'),
                'name' => 'Regis',
                'url' => 'some-url',
                'description' => 'some description',
            ]);

        $this->client->setBuildStatus(new RepositoryIdentifier(self::REPOSITORY_ID), $status, $revision);
    }

    public function testGetPullRequest()
    {
        $pullRequestsApi = $this->createMock(Repositories\PullRequests::class);
        $this->vendorClient->method('api')->with('Repositories\\PullRequests')->willReturn($pullRequestsApi);

        $apiResponse = $this->response([
            'source' => [
                'commit' => [
                    'hash' => 'source_hash',
                ],
            ],
            'destination' => [
                'commit' => [
                    'hash' => 'destination_hash',
                ],
            ],
        ]);

        $pullRequestsApi->expects($this->once())
            ->method('get')
            ->with(self::USERNAME, self::REPOSITORY_ID, 42)
            ->willReturn($apiResponse);

        $pullRequest = $this->client->getPullRequest(new RepositoryIdentifier(self::REPOSITORY_ID), 42);

        $this->assertInstanceOf(PullRequest::class, $pullRequest);
        $this->assertSame(self::REPOSITORY_ID, $pullRequest->getRepository()->value());
        $this->assertSame(42, $pullRequest->getNumber());
        $this->assertSame('source_hash', $pullRequest->getHead());
        $this->assertSame('destination_hash', $pullRequest->getBase());
    }

    private function response(array $content): MessageInterface
    {
        $response = $this->createMock(MessageInterface::class);
        $response->method('getContent')->willReturn(json_encode($content));

        return $response;
    }
}
