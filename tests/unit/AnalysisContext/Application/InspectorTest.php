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

namespace Tests\Regis\AnalysisContext\Application;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Application\Inspection;
use Regis\AnalysisContext\Application\Inspector;
use Regis\AnalysisContext\Domain\Entity\Violation;
use Regis\AnalysisContext\Domain\Model;
use Regis\AnalysisContext\Application\Vcs;

class InspectorTest extends TestCase
{
    const REVISION_HEAD = 'revision head hash';

    private $git;
    private $repository;
    private $gitRepository;
    private $diff;
    private $revisions;

    public function setUp()
    {
        $this->git = $this->createMock(Vcs\Git::class);
        $this->gitRepository = $this->createMock(Vcs\Repository::class);

        $this->diff = $this->createMock(Model\Git\Diff::class);
        $this->repository = $this->createMock(Model\Git\Repository::class);
        $this->revisions = $this->createMock(Model\Git\Revisions::class);

        $this->revisions->method('getHead')->willReturn(self::REVISION_HEAD);
        $this->git->method('getRepository')->with($this->repository)->willReturn($this->gitRepository);
        $this->gitRepository->method('getDiff')->with($this->revisions)->willReturn($this->diff);
    }

    public function testItStartsByCheckoutOutTheGitRepo()
    {
        $inspector = new Inspector($this->git);

        $this->gitRepository->expects($this->once())
            ->method('checkout')
            ->with(self::REVISION_HEAD);

        $inspector->inspect($this->repository, $this->revisions);
    }

    public function testItGivesTheDiffToTheInspectionsAndReturnsAUnifiedReport()
    {
        $inspection1 = $this->createMock(Inspection::class);
        $inspection2 = $this->createMock(Inspection::class);

        $violation1 = $this->createMock(Violation::class);
        $violation2 = $this->createMock(Violation::class);

        $inspection1->expects($this->once())
            ->method('inspectDiff')
            ->with($this->gitRepository, $this->diff)
            ->willReturn(new \ArrayIterator([$violation1]));

        $inspection2->expects($this->once())
            ->method('inspectDiff')
            ->with($this->gitRepository, $this->diff)
            ->willReturn(new \ArrayIterator([$violation2]));

        $inspector = new Inspector($this->git, [$inspection1, $inspection2]);
        $report = $inspector->inspect($this->repository, $this->revisions);

        $this->assertCount(2, iterator_to_array($report->violations()));
    }
}
