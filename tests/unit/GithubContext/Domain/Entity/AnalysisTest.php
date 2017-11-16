<?php

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
        $error = $this->error();
        $analysis = $this->analysis([$error], 1);

        $this->assertTrue($analysis->hasErrors());
        $this->assertEquals(1, $analysis->errorsCount());
        $this->assertFalse($analysis->hasWarnings());
        $this->assertEquals(0, $analysis->warningsCount());
    }

    public function testItHasWarningsIfAtLeastOneViolationIsAWarning()
    {
        $warning = $this->warning();
        $analysis = $this->analysis([$warning], 0, 1);

        $this->assertTrue($analysis->hasWarnings());
        $this->assertEquals(1, $analysis->warningsCount());
        $this->assertFalse($analysis->hasErrors());
        $this->assertEquals(0, $analysis->errorsCount());
    }

    public function testViolationsAreAccessibleAsAList()
    {
        $violations = [$this->warning(), $this->warning()];
        $analysis = $this->analysis($violations);

        $this->assertEquals($violations, $analysis->violations());
    }

    private function analysis(array $violations = [], int $errorsCount = 0, int $warningsCount = 0): Analysis
    {
        $analysis = new Analysis();
        $this->setPrivateValue($analysis, 'violations', new ArrayCollection($violations));
        $this->setPrivateValue($analysis, 'warningsCount', $warningsCount);
        $this->setPrivateValue($analysis, 'errorsCount', $errorsCount);

        return $analysis;
    }

    private function error(): Violation
    {
        $violation = $this->createMock(Violation::class);

        $violation->method('isWarning')->willReturn(false);
        $violation->method('isError')->willReturn(true);

        return $violation;
    }

    private function warning(): Violation
    {
        $violation = $this->createMock(Violation::class);

        $violation->method('isWarning')->willReturn(true);
        $violation->method('isError')->willReturn(false);

        return $violation;
    }
}
