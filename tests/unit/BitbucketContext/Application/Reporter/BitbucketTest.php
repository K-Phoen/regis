<?php

declare(strict_types=1);

namespace Tests\Regis\BitbucketContext\Application\Reporter;

use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Application\Reporter\Bitbucket as BitbucketReporter;
use Regis\BitbucketContext\Application\Bitbucket\Client as BitbucketClient;
use Regis\BitbucketContext\Application\Bitbucket\ClientFactory as BitbucketClientFactory;
use Regis\BitbucketContext\Domain\Entity\Repository;
use Regis\BitbucketContext\Domain\Model\PullRequest;
use Regis\BitbucketContext\Domain\Model\ReviewComment;

class BitbucketTest extends TestCase
{
    /** @var BitbucketClientFactory */
    private $clientFactory;

    /** @var BitbucketReporter */
    private $reporter;

    public function setUp()
    {
        $this->clientFactory = $this->createMock(BitbucketClientFactory::class);

        $this->reporter = new BitbucketReporter($this->clientFactory);
    }

    public function testViolationsAreReportedAsReviewComments()
    {
        $repository = $this->createMock(Repository::class);
        $comment = $this->createMock(ReviewComment::class);
        $pullRequest = $this->createMock(PullRequest::class);
        $client = $this->createMock(BitbucketClient::class);

        $this->clientFactory->method('createForRepository')->with($repository)->willReturn($client);

        $client->expects($this->once())
            ->method('sendComment')
            ->with($pullRequest, $comment);

        $this->reporter->report($repository, $comment, $pullRequest);
    }
}
