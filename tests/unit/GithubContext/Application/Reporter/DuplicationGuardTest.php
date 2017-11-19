<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

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
