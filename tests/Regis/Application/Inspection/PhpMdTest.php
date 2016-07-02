<?php

namespace Tests\Regis\Application\Inspection;

use Regis\Application\Inspection\PhpMd;
use Regis\Application\Inspection\PhpMdRunner;
use Regis\Domain\Entity;
use Regis\Domain\Model;

class PhpMdTest extends InspectionTestCase
{
    private $phpMd;
    /** @var PhpMd */
    private $inspection;

    public function setUp()
    {
        $this->phpMd = $this->getMockBuilder(PhpMdRunner::class)->getMock();

        $this->inspection = new PhpMd($this->phpMd);
    }

    public function testWithNoAddedFiles()
    {
        $this->phpMd->expects($this->never())->method('execute');

        $violations = iterator_to_array($this->inspection->inspectDiff($this->diff()));

        $this->assertEmpty($violations);
    }

    public function testWithASingleAddedFileButNoViolation()
    {
        $file = $this->file('test.php');
        $diff = $this->diff([$file]);

        $this->phpMd->expects($this->once())
            ->method('execute')
            ->with('test.php', $this->anything())
            ->will($this->returnValue(new \ArrayIterator()));

        $violations = iterator_to_array($this->inspection->inspectDiff($diff));

        $this->assertEmpty($violations);
    }

    public function testWithASingleAddedFileAndSomeViolations()
    {
        $file = $this->file('test.php');
        $diff = $this->diff([$file]);

        $file->method('findPositionForLine')
            ->will($this->returnCallback(function($line) {
                if ($line === 12) {
                    throw Model\Exception\LineNotInDiff::line($line);
                }

                return $line + 1;
            }));

        $this->phpMd->expects($this->once())
            ->method('execute')
            ->with('test.php', $this->anything())
            ->will($this->returnValue(new \ArrayIterator([
                // the first one is configured not to be in the diff
                [
                    'beginLine' => 12,
                    'priority' => 4,
                    'description' => 'some warning message',
                ],
                [
                    'beginLine' => 24,
                    'priority' => 4,
                    'description' => 'some warning message',
                ],
                [
                    'beginLine' => 42,
                    'priority' => 1,
                    'description' => 'some error message',
                ],
            ])));

        $violations = iterator_to_array($this->inspection->inspectDiff($diff));

        $this->assertCount(2, $violations);

        /** @var Entity\Inspection\Violation $firstViolation */
        $firstViolation = $violations[0];
        $this->assertInstanceOf(Entity\Inspection\Violation::class, $firstViolation);
        $this->assertEquals(Entity\Inspection\Violation::WARNING, $firstViolation->getSeverity());
        $this->assertTrue($firstViolation->isWarning());
        $this->assertFalse($firstViolation->isError());
        $this->assertEquals('test.php', $firstViolation->getFile());
        $this->assertEquals('some warning message', $firstViolation->getDescription());
        $this->assertEquals(24 + 1, $firstViolation->getPosition());

        /** @var Entity\Inspection\Violation $secondViolation */
        $secondViolation = $violations[1];
        $this->assertInstanceOf(Entity\Inspection\Violation::class, $secondViolation);
        $this->assertEquals(Entity\Inspection\Violation::ERROR, $secondViolation->getSeverity());
        $this->assertFalse($secondViolation->isWarning());
        $this->assertTrue($secondViolation->isError());
        $this->assertEquals('test.php', $secondViolation->getFile());
        $this->assertEquals('some error message', $secondViolation->getDescription());
        $this->assertEquals(42 + 1, $secondViolation->getPosition());
    }
}
