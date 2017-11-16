<?php

declare(strict_types=1);

namespace Tests\Regis\GithubContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Domain\Entity\Repository;
use Regis\GithubContext\Domain\Entity\PullRequestInspection;
use Regis\GithubContext\Domain\Entity\Inspection;
use Regis\GithubContext\Domain\Model\PullRequest;

class PullRequestInspectionTest extends TestCase
{
    private $repository;
    private $pullRequest;

    public function setUp()
    {
        $this->repository = $this->createMock(Repository::class);
        $this->pullRequest = $this->createMock(PullRequest::class);
    }

    public function testItHasAType()
    {
        $inspection = PullRequestInspection::create($this->repository, $this->pullRequest);

        $this->assertSame(PullRequestInspection::TYPE_GITHUB_PR, $inspection->getType());
    }

    public function testItExposesThePrNumber()
    {
        $this->pullRequest->method('getNumber')->willReturn(42);

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
