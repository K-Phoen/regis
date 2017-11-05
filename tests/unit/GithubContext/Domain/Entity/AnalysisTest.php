<?php

namespace Tests\Regis\GithubContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Domain\Entity\Analysis;
use Regis\GithubContext\Domain\Entity\Violation;

class AnalysisTest extends TestCase
{
    public function testItHasErrorsIfAtLeastOneViolationIsAnError()
    {
        $error = Violation::newError('file', 42, 9, 'description');
        $analysis = new Analysis([$error]);

        $this->assertTrue($analysis->hasErrors());
        $this->assertFalse($analysis->hasWarnings());
    }

    public function testItHasWarningsIfAtLeastOneViolationIsAWarning()
    {
        $warning = Violation::newWarning('file', 42, 9, 'description');
        $analysis = new Analysis([$warning]);

        $this->assertTrue($analysis->hasWarnings());
        $this->assertFalse($analysis->hasErrors());
    }

    public function testWarningCount()
    {
        $error = Violation::newError('file', 42, 9, 'description');
        $warning = Violation::newWarning('file', 42, 9, 'description');
        $warning2 = Violation::newWarning('other file', 42, 9, 'description');
        $analysis = new Analysis([$error, $warning, $warning2]);

        $this->assertEquals(2, $analysis->warningsCount());
    }

    public function testErrorCount()
    {
        $error = Violation::newError('file', 42, 9, 'description');
        $error2 = Violation::newError('other file', 42, 9, 'description');
        $warning = Violation::newWarning('file', 42, 9, 'description');
        $analysis = new Analysis([$error, $error2, $warning]);

        $this->assertEquals(2, $analysis->errorsCount());
    }

    /**
     * @dataProvider analysisProvider
     */
    public function testAnalysisStatus(Analysis $analysis, string $expectedStatus)
    {
        $this->assertEquals($expectedStatus, $analysis->status());
    }

    public function analysisProvider()
    {
        $error = Violation::newError('file', 42, 9, 'description');
        $warning = Violation::newWarning('file', 42, 9, 'description');

        $analysisWithOnlyWarning = new Analysis([$warning]);
        $analysisWithOnlyError = new Analysis([$error]);
        $analysisWithBoth = new Analysis([$error, $warning]);

        $analysisWithoutViolations = new Analysis();

        return [
            [$analysisWithoutViolations, Analysis::STATUS_OK],
            [$analysisWithOnlyWarning, Analysis::STATUS_WARNING],
            [$analysisWithOnlyError, Analysis::STATUS_ERROR],
            [$analysisWithBoth, Analysis::STATUS_ERROR],
        ];
    }

    public function testViolationsCanBeRetrievedByFileAndLine()
    {
        $error = Violation::newError('file', 42, 9, 'description');
        $error2 = Violation::newError('other file', 42, 9, 'description');
        $warning = Violation::newWarning('file', 42, 9, 'description');
        $warning2 = Violation::newWarning('file', 43, 9, 'description');

        $analysis = new Analysis([$error, $error2, $warning, $warning2]);

        $this->assertEmpty($analysis->violationsAtLine('inexistent file', 42));
        $this->assertEmpty($analysis->violationsAtLine('file', 20));

        $this->assertSame([$error, $warning], $analysis->violationsAtLine('file', 42));
        $this->assertSame([$error2], $analysis->violationsAtLine('other file', 42));
        $this->assertSame([$warning2], $analysis->violationsAtLine('file', 43));

        $this->assertSame([$error, $error2, $warning, $warning2], $analysis->violations());
    }
}
