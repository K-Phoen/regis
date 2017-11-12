<?php

namespace Tests\Regis\AnalysisContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Domain\Entity\Analysis;
use Regis\AnalysisContext\Domain\Entity\Violation;
use Regis\AnalysisContext\Domain\Entity\Report;

class AnalysisTest extends TestCase
{
    public function testTheStatusEvolvesWhenAddingViolations()
    {
        $error = Violation::newError('file', 42, 9, 'description');
        $warning = Violation::newWarning('file', 42, 9, 'description');
        $report = $this->createMock(Report::class);
        $analysis = new Analysis($report, 'test');

        $this->assertEquals(Analysis::STATUS_OK, $analysis->status());

        $analysis->addViolation($warning);
        $this->assertEquals(Analysis::STATUS_WARNING, $analysis->status());

        $analysis->addViolation($error);
        $this->assertEquals(Analysis::STATUS_ERROR, $analysis->status());

        $analysis->addViolation($warning);
        $this->assertEquals(Analysis::STATUS_ERROR, $analysis->status());
    }

    public function testItHasErrorsIfAtLeastOneViolationIsAnError()
    {
        $error = Violation::newError('file', 42, 9, 'description');
        $warning = Violation::newWarning('file', 42, 9, 'description');
        $report = $this->createMock(Report::class);
        $analysis = new Analysis($report, 'test');

        $this->assertFalse($analysis->hasErrors());

        $analysis->addViolation($warning);
        $this->assertFalse($analysis->hasErrors());

        $analysis->addViolation($error);
        $this->assertTrue($analysis->hasErrors());
    }

    public function testItHasWarningsIfAtLeastOneViolationIsAWarning()
    {
        $error = Violation::newError('file', 42, 9, 'description');
        $warning = Violation::newWarning('file', 42, 9, 'description');
        $report = $this->createMock(Report::class);
        $analysis = new Analysis($report, 'test');

        $this->assertFalse($analysis->hasWarnings(), 'A new analysis has no warnings');

        $analysis->addViolation($error);
        $this->assertFalse($analysis->hasWarnings());

        $analysis->addViolation($warning);
        $this->assertTrue($analysis->hasWarnings());
    }
}
