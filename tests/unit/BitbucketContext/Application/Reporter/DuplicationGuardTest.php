<?php

declare(strict_types=1);

namespace Tests\Regis\BitbucketContext\Application\Reporter;

use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Application\Inspection\ViolationsCache;
use Regis\BitbucketContext\Application\Reporter;
use Regis\BitbucketContext\Domain\Entity\Repository;
use Regis\BitbucketContext\Domain\Model\PullRequest;
use Regis\BitbucketContext\Domain\Model\ReviewComment;

class DuplicationGuardTest extends TestCase
{
    /** @var Reporter */
    private $reporter;

    /** @var ViolationsCache */
    private $violationsCache;

    /** @var Reporter\DuplicationGuard */
    private $duplicationGuard;

    public function setUp()
    {
        $this->reporter = $this->createMock(Reporter::class);
        $this->violationsCache = $this->createMock(ViolationsCache::class);

        $this->duplicationGuard = new Reporter\DuplicationGuard($this->reporter, $this->violationsCache);
    }

    public function testCachedViolationsAreNotReported()
    {
        $repository = $this->createMock(Repository::class);
        $comment = $this->createMock(ReviewComment::class);
        $pullRequest = $this->createMock(PullRequest::class);

        $this->violationsCache->method('has')->willReturn(true);

        $this->reporter->expects($this->never())->method('report');

        $this->duplicationGuard->report($repository, $comment, $pullRequest);
    }

    public function testNotCachedViolationsAreReported()
    {
        $repository = $this->createMock(Repository::class);
        $comment = $this->createMock(ReviewComment::class);
        $pullRequest = $this->createMock(PullRequest::class);

        $this->violationsCache->method('has')->willReturn(false);

        $this->reporter->expects($this->once())
            ->method('report')
            ->with($repository, $comment, $pullRequest);

        $this->violationsCache->expects($this->once())
            ->method('save')
            ->with($comment, $pullRequest);

        $this->duplicationGuard->report($repository, $comment, $pullRequest);
    }
}
