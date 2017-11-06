<?php

namespace Tests\Regis\GithubContext\Application\Reporter;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Application\Inspection\ViolationsCache;
use Regis\GithubContext\Application\Reporter;
use Regis\GithubContext\Domain\Entity\Violation;
use Regis\GithubContext\Domain\Entity\Repository;
use Regis\GithubContext\Domain\Model\PullRequest;

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
        $violation = $this->createMock(Violation::class);
        $pullRequest = $this->createMock(PullRequest::class);

        $this->violationsCache->expects($this->once())
            ->method('has')
            ->willReturn(true);

        $this->reporter->expects($this->never())->method('report');

        $this->duplicationGuard->report($repository, $violation, $pullRequest);
    }

    public function testNotCachedViolationsAreReported()
    {
        $repository = $this->createMock(Repository::class);
        $violation = $this->createMock(Violation::class);
        $pullRequest = $this->createMock(PullRequest::class);

        $this->violationsCache->expects($this->once())
            ->method('has')
            ->willReturn(false);

        $this->reporter->expects($this->once())
            ->method('report')
            ->with($repository, $violation, $pullRequest);

        $this->violationsCache->expects($this->once())
            ->method('save')
            ->with($violation, $pullRequest);

        $this->duplicationGuard->report($repository, $violation, $pullRequest);
    }
}
