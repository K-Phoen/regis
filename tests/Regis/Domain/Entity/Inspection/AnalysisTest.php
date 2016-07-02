<?php

namespace Tests\Regis\Domain\Entity\Inspection;

use Regis\Domain\Entity\Inspection\Analysis;
use Regis\Domain\Entity\Inspection\Violation;

class AnalysisTest extends \PHPUnit_Framework_TestCase
{
    public function testItHasErrorsIfAtLeastOneViolationIsAnError()
    {
        $error = Violation::newError('file', 42, 9, 'description');
        $warning = Violation::newWarning('file', 42, 9, 'description');
        $analysis = new Analysis('test');

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
        $analysis = new Analysis('test');

        $this->assertFalse($analysis->hasWarnings());

        $analysis->addViolation($error);
        $this->assertFalse($analysis->hasWarnings());

        $analysis->addViolation($warning);
        $this->assertTrue($analysis->hasWarnings());
    }

    public function testWarningCount()
    {
        $error = Violation::newError('file', 42, 9, 'description');
        $warning = Violation::newWarning('file', 42, 9, 'description');
        $warning2 = Violation::newWarning('other file', 42, 9, 'description');
        $analysis = new Analysis('test');

        $this->assertEquals(0, $analysis->warningsCount());

        $analysis->addViolation($error);
        $this->assertEquals(0, $analysis->warningsCount());

        $analysis->addViolation($warning);
        $this->assertEquals(1, $analysis->warningsCount());

        $analysis->addViolation($warning2);
        $this->assertEquals(2, $analysis->warningsCount());
    }

    public function testErrorCount()
    {
        $error = Violation::newError('file', 42, 9, 'description');
        $error2 = Violation::newError('other file', 42, 9, 'description');
        $warning = Violation::newWarning('file', 42, 9, 'description');
        $analysis = new Analysis('test');

        $this->assertEquals(0, $analysis->errorsCount());

        $analysis->addViolation($warning);
        $this->assertEquals(0, $analysis->errorsCount());

        $analysis->addViolation($error);
        $this->assertEquals(1, $analysis->errorsCount());

        $analysis->addViolation($error2);
        $this->assertEquals(2, $analysis->errorsCount());
    }

    public function testAnalysisStatus()
    {
        $error = Violation::newError('file', 42, 9, 'description');
        $warning = Violation::newWarning('file', 42, 9, 'description');
        $analysis = new Analysis('test');

        $this->assertEquals(Analysis::STATUS_OK, $analysis->getStatus());

        $analysis->addViolation($warning);
        $this->assertEquals(Analysis::STATUS_WARNING, $analysis->getStatus());

        $analysis->addViolation($error);
        $this->assertEquals(Analysis::STATUS_ERROR, $analysis->getStatus());

        $analysis->addViolation($warning);
        $this->assertEquals(Analysis::STATUS_ERROR, $analysis->getStatus());
    }
}
