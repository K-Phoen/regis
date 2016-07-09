<?php

namespace Tests\Regis\Application\Reporter;

use Regis\Application\Inspection\ViolationsCache;
use Regis\Application\Reporter;
use Regis\Domain\Entity\Inspection\Violation;
use Regis\Domain\Entity\Repository;
use Regis\Domain\Model\Github\PullRequest;

class DuplicationGuardTest extends \PHPUnit_Framework_TestCase
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
            ->will($this->returnValue(true));

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
            ->will($this->returnValue(false));

        $this->reporter->expects($this->once())
            ->method('report')
            ->with($repository, $violation, $pullRequest);

        $this->violationsCache->expects($this->once())
            ->method('save')
            ->with($violation, $pullRequest);

        $this->duplicationGuard->report($repository, $violation, $pullRequest);
    }
}
