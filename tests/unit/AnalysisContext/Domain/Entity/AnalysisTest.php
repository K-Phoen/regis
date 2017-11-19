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

namespace Tests\Regis\AnalysisContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Domain\Entity\Analysis;
use Regis\AnalysisContext\Domain\Entity\Violation;
use Regis\AnalysisContext\Domain\Entity\Report;

class AnalysisTest extends TestCase
{
    public function testAnIdentifierIsGenerated()
    {
        $analysis = new Analysis(new Report('dummy diff'), 'test');

        $this->assertNotEmpty($analysis->id());
    }

    public function testTheTypeIsSaved()
    {
        $analysis = new Analysis(new Report('dummy diff'), 'test');

        $this->assertSame('test', $analysis->type());
    }

    public function testTheErrorsAndWarningsCountsEvolvesWhenAddingViolations()
    {
        $error = Violation::newError('file', 42, 9, 'description');
        $warning = Violation::newWarning('file', 42, 9, 'description');
        $report = new Report('dummy diff');
        $analysis = new Analysis($report, 'test');

        $this->assertSame(0, $analysis->errorsCount());
        $this->assertSame(0, $analysis->warningsCount());

        $analysis->addViolation($warning);
        $this->assertSame(0, $analysis->errorsCount());
        $this->assertSame(1, $analysis->warningsCount());

        $analysis->addViolation($error);
        $this->assertSame(1, $analysis->errorsCount());
        $this->assertSame(1, $analysis->warningsCount());

        $analysis->addViolation($warning);
        $this->assertSame(1, $analysis->errorsCount());
        $this->assertSame(2, $analysis->warningsCount());
    }

    public function testItKnowsIfThereAreWarningsAndErrors()
    {
        $error = Violation::newError('file', 42, 9, 'description');
        $warning = Violation::newWarning('file', 42, 9, 'description');
        $report = new Report('dummy diff');
        $analysis = new Analysis($report, 'test');

        $this->assertFalse($analysis->hasErrors());
        $this->assertFalse($analysis->hasWarnings());

        $analysis->addViolation($warning);
        $this->assertFalse($analysis->hasErrors());
        $this->assertTrue($analysis->hasWarnings());

        $analysis->addViolation($error);
        $this->assertTrue($analysis->hasErrors());
        $this->assertTrue($analysis->hasWarnings());
    }
}
