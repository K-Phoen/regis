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

namespace Tests\Regis\AppContext\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Regis\AppContext\Domain\Entity\Analysis;
use Regis\AppContext\Domain\Entity\Violation;
use Tests\Regis\Helper\ObjectManipulationHelper;

class AnalysisTest extends TestCase
{
    use ObjectManipulationHelper;

    public function testItHasErrorsIfAtLeastOneViolationIsAnError()
    {
        $error = $this->error('file', 42);
        $analysis = $this->analysis([$error], 1);

        $this->assertTrue($analysis->hasErrors());
        $this->assertSame(1, $analysis->errorsCount());
        $this->assertFalse($analysis->hasWarnings());
        $this->assertSame(0, $analysis->warningsCount());
    }

    public function testItHasWarningsIfAtLeastOneViolationIsAWarning()
    {
        $warning = $this->warning('file', 42);
        $analysis = $this->analysis([$warning], 0, 1);

        $this->assertTrue($analysis->hasWarnings());
        $this->assertSame(1, $analysis->warningsCount());
        $this->assertFalse($analysis->hasErrors());
        $this->assertSame(0, $analysis->errorsCount());
    }

    /**
     * @dataProvider analysisProvider
     */
    public function testAnalysisStatus(Analysis $analysis, string $expectedStatus)
    {
        $this->assertSame($expectedStatus, $analysis->status());
    }

    public function analysisProvider()
    {
        $error = $this->error('file', 42);
        $warning = $this->warning('file', 42);

        $analysisWithOnlyWarning = $this->analysis([$warning], 0, 1);
        $analysisWithOnlyError = $this->analysis([$error], 1);
        $analysisWithBoth = $this->analysis([$error, $warning], 1, 1);

        $analysisWithoutViolations = $this->analysis();

        return [
            [$analysisWithoutViolations, Analysis::STATUS_OK],
            [$analysisWithOnlyWarning, Analysis::STATUS_WARNING],
            [$analysisWithOnlyError, Analysis::STATUS_ERROR],
            [$analysisWithBoth, Analysis::STATUS_ERROR],
        ];
    }

    public function testViolationsCanBeRetrievedByFileAndLine()
    {
        $error = $this->error('file', 42);
        $error2 = $this->error('other file', 42);
        $warning = $this->warning('file', 42);
        $warning2 = $this->warning('file', 43);

        $analysis = $this->analysis([$error, $error2, $warning, $warning2]);

        $this->assertEmpty($analysis->violationsAtLine('inexistent file', 42));
        $this->assertEmpty($analysis->violationsAtLine('file', 20));

        $this->assertSame([$error, $warning], $analysis->violationsAtLine('file', 42));
        $this->assertSame([$error2], $analysis->violationsAtLine('other file', 42));
        $this->assertSame([$warning2], $analysis->violationsAtLine('file', 43));

        $this->assertSame([$error, $error2, $warning, $warning2], $analysis->violations());
    }

    private function analysis(array $violations = [], int $errorsCount = 0, int $warningsCount = 0): Analysis
    {
        $analysis = new Analysis();
        $this->setPrivateValue($analysis, 'violations', new ArrayCollection($violations));
        $this->setPrivateValue($analysis, 'warningsCount', $warningsCount);
        $this->setPrivateValue($analysis, 'errorsCount', $errorsCount);

        return $analysis;
    }

    private function error(string $file, int $line): Violation
    {
        $violation = $this->createMock(Violation::class);

        $violation->method('isError')->willReturn(true);
        $violation->method('file')->willReturn($file);
        $violation->method('line')->willReturn($line);

        return $violation;
    }

    private function warning(string $file, int $line): Violation
    {
        $violation = $this->createMock(Violation::class);

        $violation->method('isError')->willReturn(false);
        $violation->method('file')->willReturn($file);
        $violation->method('line')->willReturn($line);

        return $violation;
    }
}
