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
        $inspection = PullRequestInspection::create($this->repository, $this->pullRequest, 4);

        $this->assertSame(PullRequestInspection::TYPE_GITHUB_PR, $inspection->getType());
    }

    public function testItExposesThePrNumber()
    {
        $this->pullRequest->method('getNumber')->willReturn(42);

        $inspection = PullRequestInspection::create($this->repository, $this->pullRequest, 4);

        $this->assertSame(42, $inspection->getPullRequestNumber());
    }

    public function testItIsInitializedCorrectly()
    {
        $inspection = PullRequestInspection::create($this->repository, $this->pullRequest, 4);

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
