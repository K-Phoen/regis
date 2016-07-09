<?php

namespace Tests\Regis\Application\Reporter;

use Regis\Application\Reporter\Github as GithubReporter;
use Regis\Application\Github\Client as GithubClient;
use Regis\Application\Github\ClientFactory as GithubClientFactory;
use Regis\Domain\Entity\Github\Repository;
use Regis\Domain\Entity\Inspection\Violation;
use Regis\Domain\Model\Github\PullRequest;

class GithubTest extends \PHPUnit_Framework_TestCase
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
            ->will($this->returnValue($client));

        $this->reporter->report($repository, $violation, $pullRequest);
    }
}
