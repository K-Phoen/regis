<?php

namespace Tests\Regis\Application\Reporter;

use Regis\Application\Reporter\Github as GithubReporter;
use Regis\Application\Github\Client as GithubClient;
use Regis\Domain\Model\Github\PullRequest;
use Regis\Domain\Entity\Inspection\Violation;

class GithubTest extends \PHPUnit_Framework_TestCase
{
    /** @var GithubClient */
    private $client;

    /** @var GithubReporter */
    private $reporter;

    public function setUp()
    {
        $this->client = $this->createMock(GithubClient::class);

        $this->reporter = new GithubReporter($this->client);
    }

    public function testViolationsAreReportedAsReviewCOmments()
    {
        $violation = $this->createMock(Violation::class);
        $pullRequest = $this->createMock(PullRequest::class);

        $this->client->expects($this->once())->method('sendComment');

        $this->reporter->report($violation, $pullRequest);
    }
}
