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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Domain\Entity\Analysis;
use Regis\GithubContext\Domain\Entity\Violation;
use Tests\Regis\Helper\ObjectManipulationHelper;

class AnalysisTest extends TestCase
{
    use ObjectManipulationHelper;

    public function testItHasErrorsIfAtLeastOneViolationIsAnError()
    {
        $analysis = $this->analysis(1);

        $this->assertTrue($analysis->hasErrors());
        $this->assertSame(1, $analysis->errorsCount());
        $this->assertFalse($analysis->hasWarnings());
        $this->assertSame(0, $analysis->warningsCount());
    }

    public function testItHasWarningsIfAtLeastOneViolationIsAWarning()
    {
        $analysis = $this->analysis(0, 1);

        $this->assertTrue($analysis->hasWarnings());
        $this->assertSame(1, $analysis->warningsCount());
        $this->assertFalse($analysis->hasErrors());
        $this->assertSame(0, $analysis->errorsCount());
    }

    public function testViolationsAreAccessibleAsAList()
    {
        $violations = [$this->violation(), $this->violation()];
        $analysis = $this->analysis(2, 0, $violations);

        $this->assertSame($violations, $analysis->violations());
    }

    private function analysis(int $errorsCount = 0, int $warningsCount = 0, array $violations = []): Analysis
    {
        $analysis = new Analysis();
        $this->setPrivateValue($analysis, 'violations', new ArrayCollection($violations));
        $this->setPrivateValue($analysis, 'warningsCount', $warningsCount);
        $this->setPrivateValue($analysis, 'errorsCount', $errorsCount);

        return $analysis;
    }

    private function violation(): Violation
    {
        return $this->createMock(Violation::class);
    }
}
