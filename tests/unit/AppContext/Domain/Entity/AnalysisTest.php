<?php

namespace Tests\Regis\AppContext\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Regis\AppContext\Domain\Entity\Analysis;
use Regis\AppContext\Domain\Entity\Violation;
use Tests\Regis\Helper\ObjectManipulationHelper;

class AnalysisTest extends TestCase
{
    use ObjectManipulationHelper;

    public function testItHasErrorsIfAtLeastOneViolationIsAnError()
    {
        $error = $this->error('file', 42, 9);
        $analysis = $this->analysis([$error], 1);

        $this->assertTrue($analysis->hasErrors());
        $this->assertEquals(1, $analysis->errorsCount());
        $this->assertFalse($analysis->hasWarnings());
        $this->assertEquals(0, $analysis->warningsCount());
    }

    public function testItHasWarningsIfAtLeastOneViolationIsAWarning()
    {
        $warning = $this->warning('file', 42, 9);
        $analysis = $this->analysis([$warning], 0, 1);

        $this->assertTrue($analysis->hasWarnings());
        $this->assertEquals(1, $analysis->warningsCount());
        $this->assertFalse($analysis->hasErrors());
        $this->assertEquals(0, $analysis->errorsCount());
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
        $error = $this->error('file', 42, 9);
        $warning = $this->warning('file', 42, 9);

        $analysisWithOnlyWarning = $this->analysis([$warning], 0, 1);
        $analysisWithOnlyError = $this->analysis([$error], 1);
        $analysisWithBoth = $this->analysis([$error, $warning], 1, 1);

        $analysisWithoutViolations = $this->analysis();

        return [
            [$analysisWithoutViolations, Analysis::STATUS_OK],
            [$analysisWithOnlyWarning, Analysis::STATUS_WARNING],
            [$analysisWithOnlyError, Analysis::STATUS_ERROR],
            [$analysisWithBoth, Analysis::STATUS_ERROR],
        ];
    }

    public function testViolationsCanBeRetrievedByFileAndLine()
    {
        $error = $this->error('file', 42, 9);
        $error2 = $this->error('other file', 42, 9);
        $warning = $this->warning('file', 42, 9);
        $warning2 = $this->warning('file', 43, 9);

        $analysis = $this->analysis([$error, $error2, $warning, $warning2]);

        $this->assertEmpty($analysis->violationsAtLine('inexistent file', 42));
        $this->assertEmpty($analysis->violationsAtLine('file', 20));

        $this->assertSame([$error, $warning], $analysis->violationsAtLine('file', 42));
        $this->assertSame([$error2], $analysis->violationsAtLine('other file', 42));
        $this->assertSame([$warning2], $analysis->violationsAtLine('file', 43));

        $this->assertSame([$error, $error2, $warning, $warning2], $analysis->violations());
    }

    private function analysis(array $violations = [], int $errorsCount = 0, int $warningsCount = 0): Analysis
    {
        $analysis = new Analysis();
        $this->setPrivateValue($analysis, 'violations', new ArrayCollection($violations));
        $this->setPrivateValue($analysis, 'warningsCount', $warningsCount);
        $this->setPrivateValue($analysis, 'errorsCount', $errorsCount);

        return $analysis;
    }

    private function error(string $file, int $line, int $position): Violation
    {
        $violation = $this->createMock(Violation::class);

        $violation->method('isWarning')->willReturn(false);
        $violation->method('isError')->willReturn(true);
        $violation->method('file')->willReturn($file);
        $violation->method('line')->willReturn($line);
        $violation->method('position')->willReturn($position);

        return $violation;
    }

    private function warning(string $file, int $line, int $position): Violation
    {
        $violation = $this->createMock(Violation::class);

        $violation->method('isWarning')->willReturn(true);
        $violation->method('isError')->willReturn(false);
        $violation->method('file')->willReturn($file);
        $violation->method('line')->willReturn($line);
        $violation->method('position')->willReturn($position);

        return $violation;
    }
}
