<?php

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
