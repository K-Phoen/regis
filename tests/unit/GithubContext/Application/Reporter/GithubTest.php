<?php

declare(strict_types=1);

namespace Tests\Regis\GithubContext\Application\Reporter;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Application\Reporter\Github as GithubReporter;
use Regis\GithubContext\Application\Github\Client as GithubClient;
use Regis\GithubContext\Application\Github\ClientFactory as GithubClientFactory;
use Regis\GithubContext\Domain\Entity\Repository;
use Regis\GithubContext\Domain\Entity\Violation;
use Regis\GithubContext\Domain\Model\PullRequest;

class GithubTest extends TestCase
{
    /** @var GithubClientFactory */
    private $clientFactory;

    /** @var GithubReporter */
    private $reporter;

    public function setUp()
    {
        $this->clientFactory = $this->createMock(GithubClientFactory::class);

        $this->reporter = new GithubReporter($this->clientFactory);
    }

    public function testViolationsAreReportedAsReviewCOmments()
    {
        $repository = $this->createMock(Repository::class);
        $violation = $this->createMock(Violation::class);
        $pullRequest = $this->createMock(PullRequest::class);

        $client = $this->createMock(GithubClient::class);
        $client->expects($this->once())->method('sendComment');

        $this->clientFactory->expects($this->once())
            ->method('createForRepository')
            ->with($repository)
            ->willReturn($client);

        $this->reporter->report($repository, $violation, $pullRequest);
    }
}
