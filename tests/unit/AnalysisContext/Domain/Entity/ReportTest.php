<?php

declare(strict_types=1);

namespace Tests\Regis\AnalysisContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Domain\Entity\Analysis;
use Regis\AnalysisContext\Domain\Entity\Report;

class ReportTest extends TestCase
{
    public function testAnIdentifierIsGenerated()
    {
        $report = new Report('dummy diff');

        $this->assertNotEmpty($report->id());
    }

    public function testReportStatusEvolvesWhenAddingAnalyses()
    {
        $okAnalysis = $this->analysisMock(Analysis::STATUS_OK);
        $warningAnalysis = $this->analysisMock(Analysis::STATUS_WARNING);
        $warningAnalysis2 = $this->analysisMock(Analysis::STATUS_WARNING);
        $errorAnalysis = $this->analysisMock(Analysis::STATUS_ERROR);
        $report = new Report('raw diff');

        $this->assertSame(Report::STATUS_OK, $report->status());

        $report->addAnalysis($okAnalysis);
        $this->assertSame(Analysis::STATUS_OK, $report->status());

        $report->addAnalysis($warningAnalysis);
        $this->assertSame(Analysis::STATUS_WARNING, $report->status());

        $report->addAnalysis($errorAnalysis);
        $this->assertSame(Analysis::STATUS_ERROR, $report->status());

        $report->addAnalysis($warningAnalysis2);
        $this->assertSame(Analysis::STATUS_ERROR, $report->status());
    }

    public function testItCountsTheErrorsAndWarnings()
    {
        $okAnalysis = $this->analysisMock(Analysis::STATUS_OK);
        $warningAnalysis = $this->analysisMock(Analysis::STATUS_WARNING, 0, 3);
        $errorAnalysis = $this->analysisMock(Analysis::STATUS_ERROR, 2);
        $report = new Report('raw diff');

        $this->assertSame(0, $report->warningsCount());
        $this->assertSame(0, $report->errorsCount());

        $report->addAnalysis($okAnalysis);
        $this->assertSame(0, $report->warningsCount());
        $this->assertSame(0, $report->errorsCount());

        $report->addAnalysis($warningAnalysis);
        $this->assertSame(3, $report->warningsCount());
        $this->assertSame(0, $report->errorsCount());

        $report->addAnalysis($errorAnalysis);
        $this->assertSame(3, $report->warningsCount());
        $this->assertSame(2, $report->errorsCount());
    }

    private function analysisMock(string $status, int $errorsCount = 0, int $warningsCount = 0): Analysis
    {
        $analysis = $this->createMock(Analysis::class);

        $analysis->method('hasWarnings')->willReturn($status === Analysis::STATUS_WARNING);
        $analysis->method('hasErrors')->willReturn($status === Analysis::STATUS_ERROR);
        $analysis->method('warningsCount')->willReturn($warningsCount);
        $analysis->method('errorsCount')->willReturn($errorsCount);

        return $analysis;
    }
}
