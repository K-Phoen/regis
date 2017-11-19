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

namespace Tests\Regis\AnalysisContext\Application\CommandHandler;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Application\Inspector;
use Regis\AnalysisContext\Application\Command;
use Regis\AnalysisContext\Application\CommandHandler;
use Regis\AnalysisContext\Domain\Entity\Report;
use Regis\AnalysisContext\Domain\Model;

class RunAnalysesTest extends TestCase
{
    /** @var Inspector */
    private $inspector;

    /** @var CommandHandler\RunAnalyses */
    private $handler;

    public function setUp()
    {
        $this->inspector = $this->createMock(Inspector::class);

        $this->handler = new CommandHandler\RunAnalyses($this->inspector);
    }

    public function testTheInspectionIsDelegatedToTheInspector()
    {
        $report = $this->createMock(Report::class);
        $repository = $this->createMock(Model\Git\Repository::class);
        $revisions = $this->createMock(Model\Git\Revisions::class);

        $this->inspector->expects($this->once())
            ->method('inspect')
            ->with($repository, $revisions)
            ->willReturn($report);

        $this->assertSame($report, $this->handler->handle(new Command\RunAnalyses($repository, $revisions)));
    }
}
