<?php

namespace Tests\Regis\Domain\Entity\Github;

use Regis\Domain\Entity\Github\Repository;
use Regis\Domain\Entity\Github\PullRequestInspection;
use Regis\Domain\Entity\Inspection;
use Regis\Domain\Model\Github\PullRequest;

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

    public function testStart()
    {
        $inspection = PullRequestInspection::create($this->repository, $this->pullRequest);

        $this->assertNull($inspection->getStartedAt());
        $this->assertSame(Inspection::STATUS_SCHEDULED, $inspection->getStatus());

        $inspection->start();

        $this->assertNotNull($inspection->getStartedAt());
        $this->assertSame(Inspection::STATUS_STARTED, $inspection->getStatus());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage This inspection is already started
     */
    public function testStartCanBeCalledOnlyOnce()
    {
        $inspection = PullRequestInspection::create($this->repository, $this->pullRequest);

        $inspection->start();
        $inspection->start();
    }

    public function testFinish()
    {
        $inspection = PullRequestInspection::create($this->repository, $this->pullRequest);

        $this->assertNull($inspection->getFinishedAt());
        $this->assertSame(Inspection::STATUS_SCHEDULED, $inspection->getStatus());

        $inspection->finish();

        $this->assertNotNull($inspection->getFinishedAt());
        $this->assertSame(Inspection::STATUS_FINISHED, $inspection->getStatus());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage This inspection is already finished
     */
    public function testFinishCanBeCalledOnlyOnce()
    {
        $inspection = PullRequestInspection::create($this->repository, $this->pullRequest);

        $inspection->finish();
        $inspection->finish();
    }

    public function testFail()
    {
        $inspection = PullRequestInspection::create($this->repository, $this->pullRequest);

        $this->assertNull($inspection->getFinishedAt());
        $this->assertSame(Inspection::STATUS_SCHEDULED, $inspection->getStatus());

        $inspection->fail(new \Exception());

        $this->assertNotNull($inspection->getFinishedAt());
        $this->assertSame(Inspection::STATUS_FAILED, $inspection->getStatus());
        $this->assertNotEmpty($inspection->getFailureTrace());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage This inspection is already finished
     */
    public function testFailhCanBeCalledOnlyOnce()
    {
        $inspection = PullRequestInspection::create($this->repository, $this->pullRequest);

        $inspection->fail(new \Exception());
        $inspection->fail(new \Exception());
    }
}
