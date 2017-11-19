<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Tests\Regis\BitbucketContext\Infrastructure\Github;

use Bitbucket\API\Http\ClientInterface;
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
use Regis\BitbucketContext\Domain\Model\ReviewComment;

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

    public function testCreateWebhook()
    {
        $hookUrl = 'hook-url';
        $hooksApi = $this->createMock(Repositories\Hooks::class);

        $this->vendorClient->method('api')->with('Repositories\\Hooks')->willReturn($hooksApi);

        $hooksApi->expects($this->once())
            ->method('create')
            ->with(self::USERNAME, self::REPOSITORY_ID, [
                'description' => 'Regis webhook',
                'url' => $hookUrl,
                'active' => true,
                'events' => [
                    'pullrequest:created',
                    'pullrequest:updated',
                    'pullrequest:rejected',
                    'pullrequest:fulfilled',
                ],
            ])
            ->willReturn($this->response([]));

        $this->client->createWebhook(new RepositoryIdentifier(self::REPOSITORY_ID), $hookUrl);
    }

    public function testAddDeployKey()
    {
        $keyContent = 'key-content';
        $keyTitle = 'key-title';
        $deployKeysApi = $this->createMock(Repositories\Deploykeys::class);

        $this->vendorClient->method('api')->with('Repositories\\Deploykeys')->willReturn($deployKeysApi);

        $deployKeysApi->expects($this->once())
            ->method('create')
            ->with(self::USERNAME, self::REPOSITORY_ID, $keyContent, $keyTitle)
            ->willReturn($this->response([]));

        $this->client->addDeployKey(new RepositoryIdentifier(self::REPOSITORY_ID), $keyTitle, $keyContent);
    }

    public function testSendComment()
    {
        $pullRequest = new PullRequest(new RepositoryIdentifier(self::REPOSITORY_ID), 42, 'head sha', 'base sha');
        $comment = new ReviewComment('file.php', 2, 'content');

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->method('setApiVersion')->willReturnSelf();

        $this->vendorClient->method('getClient')->willReturn($httpClient);

        $httpClient->expects($this->once())
            ->method('post')
            ->with(
                sprintf('repositories/%s/%s/pullrequests/%d/comments/', self::USERNAME, self::REPOSITORY_ID, 42),
                [
                    'anchor' => 'head sha',
                    'dest_rev' => 'base sha',
                    'line_to' => 2,
                    'filename' => 'file.php',
                    'content' => 'content',
                ]
            );

        $this->client->sendComment($pullRequest, $comment);
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

    public function testGetCloneUrl()
    {
        $repositoriesApi = $this->createMock(Repositories\Repository::class);
        $this->vendorClient->method('api')->with('Repositories\\Repository')->willReturn($repositoriesApi);

        $apiResponse = $this->response([
            'uuid' => self::REPOSITORY_ID,
            'name' => 'repository-name',
            'links' => [
                'clone' => [
                    [
                        'name' => 'https',
                        'href' => 'https-clone-url',
                    ],
                    [
                        'name' => 'ssh',
                        'href' => 'ssh-clone-url',
                    ],
                ],
                'html' => [
                    'href' => 'public-url',
                ],
            ],
        ]);

        $repositoriesApi->expects($this->once())
            ->method('get')
            ->with(self::USERNAME, self::REPOSITORY_ID)
            ->willReturn($apiResponse);

        $cloneUrl = $this->client->getCloneUrl(new RepositoryIdentifier(self::REPOSITORY_ID));
        $this->assertSame('ssh-clone-url', $cloneUrl);
    }

    private function response(array $content): MessageInterface
    {
        $response = $this->createMock(MessageInterface::class);
        $response->method('getContent')->willReturn(json_encode($content));

        return $response;
    }
}
