<?php

namespace Tests\Regis\Application\Entity\Inspection;

use Regis\Application\Entity\Inspection\Analysis;
use Regis\Application\Entity\Inspection\Report;

class ReportTest extends \PHPUnit_Framework_TestCase
{
    public function testReportStatus()
    {
        $okAnalysis = $this->analysisMock(Analysis::STATUS_OK);
        $warningAnalysis = $this->analysisMock(Analysis::STATUS_WARNING);
        $warningAnalysis2 = $this->analysisMock(Analysis::STATUS_WARNING);
        $errorAnalysis = $this->analysisMock(Analysis::STATUS_ERROR);
        $report = new Report();

        $this->assertEquals(Report::STATUS_OK, $report->getStatus());

        $report->addAnalysis($okAnalysis);
        $this->assertEquals(Analysis::STATUS_OK, $report->getStatus());

        $report->addAnalysis($warningAnalysis);
        $this->assertEquals(Analysis::STATUS_WARNING, $report->getStatus());

        $report->addAnalysis($errorAnalysis);
        $this->assertEquals(Analysis::STATUS_ERROR, $report->getStatus());

        $report->addAnalysis($warningAnalysis2);
        $this->assertEquals(Analysis::STATUS_ERROR, $report->getStatus());
    }

    public function testHasErrorsAndWarnings()
    {
        $okAnalysis = $this->analysisMock(Analysis::STATUS_OK);
        $warningAnalysis = $this->analysisMock(Analysis::STATUS_WARNING);
        $errorAnalysis = $this->analysisMock(Analysis::STATUS_ERROR);
        $report = new Report();

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
        $analysis2= $this->analysisMock(Analysis::STATUS_ERROR, $warnings = 3, $errors = 2);
        $report = new Report();

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
        $analysis = $this->getMockBuilder(Analysis::class)->disableOriginalConstructor()->getMock();

        $analysis->expects($this->any())
            ->method('hasWarnings')
            ->will($this->returnValue($status === Analysis::STATUS_WARNING));
        $analysis->expects($this->any())
            ->method('warningsCount')
            ->will($this->returnValue($warnings));

        $analysis->expects($this->any())
            ->method('hasErrors')
            ->will($this->returnValue($status === Analysis::STATUS_ERROR));
        $analysis->expects($this->any())
            ->method('errorsCount')
            ->will($this->returnValue($errors));

        return $analysis;
    }
}
