<?php

namespace Tests\Regis\AnalysisContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Domain\Entity\Analysis;
use Regis\AnalysisContext\Domain\Entity\Violation;
use Regis\AnalysisContext\Domain\Entity\Report;

class AnalysisTest extends TestCase
{
    public function testTheErrorsAndWarningsCountsEvolvesWhenAddingViolations()
    {
        $error = Violation::newError('file', 42, 9, 'description');
        $warning = Violation::newWarning('file', 42, 9, 'description');
        $report = new Report('dummy diff');
        $analysis = new Analysis($report, 'test');

        $this->assertEquals(0, $analysis->errorsCount());
        $this->assertEquals(0, $analysis->warningsCount());

        $analysis->addViolation($warning);
        $this->assertEquals(0, $analysis->errorsCount());
        $this->assertEquals(1, $analysis->warningsCount());

        $analysis->addViolation($error);
        $this->assertEquals(1, $analysis->errorsCount());
        $this->assertEquals(1, $analysis->warningsCount());

        $analysis->addViolation($warning);
        $this->assertEquals(1, $analysis->errorsCount());
        $this->assertEquals(2, $analysis->warningsCount());
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
