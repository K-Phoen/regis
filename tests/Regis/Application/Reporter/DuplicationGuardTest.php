<?php

namespace Tests\Regis\Application\Reporter;

use Regis\Application\Inspection\ViolationsCache;
use Regis\Application\Model\Github\PullRequest;
use Regis\Application\Model\Violation;
use Regis\Application\Reporter;

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
        $violation = $this->createMock(Violation::class);
        $pullRequest = $this->createMock(PullRequest::class);

        $this->violationsCache->expects($this->once())
            ->method('has')
            ->will($this->returnValue(true));

        $this->reporter->expects($this->never())->method('report');

        $this->duplicationGuard->report($violation, $pullRequest);
    }

    public function testNotCachedViolationsAreReported()
    {
        $violation = $this->createMock(Violation::class);
        $pullRequest = $this->createMock(PullRequest::class);

        $this->violationsCache->expects($this->once())
            ->method('has')
            ->will($this->returnValue(false));

        $this->reporter->expects($this->once())
            ->method('report')
            ->with($violation, $pullRequest);

        $this->violationsCache->expects($this->once())
            ->method('save')
            ->with($violation, $pullRequest);

        $this->duplicationGuard->report($violation, $pullRequest);
    }
}
