<?php

namespace Tests\Regis\GithubContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Domain\Entity\Analysis;
use Regis\AnalysisContext\Domain\Entity\Report;

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

    private function analysisMock($status): Analysis
    {
        $analysis = $this->createMock(Analysis::class);

        $analysis->method('hasWarnings')->willReturn($status === Analysis::STATUS_WARNING);
        $analysis->method('hasErrors')->willReturn($status === Analysis::STATUS_ERROR);

        return $analysis;
    }
}
