<?php

namespace Tests\Regis\AnalysisContext\Domain\Model\Inspection;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Domain\Model\Inspection\Analysis;
use Regis\AnalysisContext\Domain\Model\Inspection\Report;

class ReportTest extends TestCase
{
    public function testReportStatus()
    {
        $okAnalysis = $this->analysisMock(Analysis::STATUS_OK);
        $warningAnalysis = $this->analysisMock(Analysis::STATUS_WARNING);
        $warningAnalysis2 = $this->analysisMock(Analysis::STATUS_WARNING);
        $errorAnalysis = $this->analysisMock(Analysis::STATUS_ERROR);
        $report = new Report('raw diff');

        $this->assertEquals(Report::STATUS_OK, $report->status());

        $report->addAnalysis($okAnalysis);
        $this->assertEquals(Analysis::STATUS_OK, $report->status());

        $report->addAnalysis($warningAnalysis);
        $this->assertEquals(Analysis::STATUS_WARNING, $report->status());

        $report->addAnalysis($errorAnalysis);
        $this->assertEquals(Analysis::STATUS_ERROR, $report->status());

        $report->addAnalysis($warningAnalysis2);
        $this->assertEquals(Analysis::STATUS_ERROR, $report->status());
    }

    public function testHasErrorsAndWarnings()
    {
        $okAnalysis = $this->analysisMock(Analysis::STATUS_OK);
        $warningAnalysis = $this->analysisMock(Analysis::STATUS_WARNING);
        $errorAnalysis = $this->analysisMock(Analysis::STATUS_ERROR);
        $report = new Report('raw diff');

        $this->assertFalse($report->hasErrors());
        $this->assertFalse($report->hasWarnings());

        $report->addAnalysis($okAnalysis);
        $this->assertFalse($report->hasErrors());
        $this->assertFalse($report->hasWarnings());

        $report->addAnalysis($warningAnalysis);
        $this->assertFalse($report->hasErrors());
        $this->assertTrue($report->hasWarnings());

        $report->addAnalysis($errorAnalysis);
        $this->assertTrue($report->hasErrors());
        $this->assertTrue($report->hasWarnings());
    }

    public function testErrorsAndWarningsCounts()
    {
        $analysis = $this->analysisMock(Analysis::STATUS_ERROR, $warnings = 5, $errors = 5);
        $analysis2 = $this->analysisMock(Analysis::STATUS_ERROR, $warnings = 3, $errors = 2);
        $report = new Report('raw diff');

        $this->assertEquals(0, $report->errorsCount());
        $this->assertEquals(0, $report->warningsCount());

        $report->addAnalysis($analysis);
        $this->assertEquals(5, $report->errorsCount());
        $this->assertEquals(5, $report->warningsCount());

        $report->addAnalysis($analysis2);
        $this->assertEquals(7, $report->errorsCount());
        $this->assertEquals(8, $report->warningsCount());
    }

    private function analysisMock($status, $warnings = 0, $errors = 0): Analysis
    {
        $analysis = $this->createMock(Analysis::class);

        $analysis->method('hasWarnings')->willReturn($status === Analysis::STATUS_WARNING);
        $analysis->method('warningsCount')->willReturn($warnings);

        $analysis->method('hasErrors')->willReturn($status === Analysis::STATUS_ERROR);
        $analysis->method('errorsCount')->willReturn($errors);

        return $analysis;
    }
}
