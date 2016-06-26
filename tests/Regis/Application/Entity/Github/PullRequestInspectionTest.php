<?php

namespace Tests\Regis\Application\Entity\Github;

use Regis\Application\Entity\Github\Repository;
use Regis\Application\Entity\Github\PullRequestInspection;
use Regis\Application\Entity\Inspection;
use Regis\Application\Model\Github\PullRequest;

class PullRequestInspectionTest extends \PHPUnit_Framework_TestCase
{
    private $repository;
    private $pullRequest;

    public function setUp()
    {
        $this->repository = $this->getMockBuilder(Repository::class)->disableOriginalConstructor()->getMock();
        $this->pullRequest = $this->getMockBuilder(PullRequest::class)->disableOriginalConstructor()->getMock();
    }

    public function testItHasAType()
    {
        $inspection = PullRequestInspection::create($this->repository, $this->pullRequest);

        $this->assertSame(PullRequestInspection::TYPE_GITHUB_PR, $inspection->getType());
    }

    public function testItExposesThePrNumber()
    {
        $this->pullRequest->expects($this->once())
            ->method('getNumber')
            ->will($this->returnValue(42));

        $inspection = PullRequestInspection::create($this->repository, $this->pullRequest);

        $this->assertSame(42, $inspection->getPullRequestNumber());
    }

    public function testItIsInitializedCorrectly()
    {
        $inspection = PullRequestInspection::create($this->repository, $this->pullRequest);

        $this->assertNotEmpty($inspection->getId());
        $this->assertEmpty($inspection->getFailureTrace());
        $this->assertSame(Inspection::STATUS_SCHEDULED, $inspection->getStatus());
        $this->assertSame($this->repository, $inspection->getRepository());
        $this->assertFalse($inspection->hasReport());
        $this->assertNotNull($inspection->getCreatedAt());
        $this->assertNull($inspection->getStartedAt());
        $this->assertNull($inspection->getFinishedAt());
    }
}
