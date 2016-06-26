<?php

namespace Tests\Regis\Application\Reporter;

use Regis\Application\Model\Github\PullRequest;
use Regis\Application\Entity\Inspection\Violation;
use Regis\Application\Reporter\Github as GithubReporter;
use Regis\Github\Client as GithubClient;

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
