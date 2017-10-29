<?php

namespace Tests\Regis\Application\Inspection;

use Regis\Application\Inspection\Phpstan;
use Regis\Application\Inspection\PhpstanRunner;
use Regis\Domain\Entity;
use Regis\Domain\Model;

class PhpstanTest extends InspectionTestCase
{
    private $phpstan;

    /** @var Phpstan */
    private $inspection;

    public function setUp()
    {
        $this->phpstan = $this->createMock(PhpstanRunner::class);

        $this->inspection = new Phpstan($this->phpstan);
    }

    public function testItHasAType()
    {
        $this->assertEquals('phpstan', $this->inspection->getType());
    }

    public function testWithNoAddedFiles()
    {
        $this->phpstan->expects($this->never())->method('execute');

        $violations = iterator_to_array($this->inspection->inspectDiff($this->diff()));

        $this->assertEmpty($violations);
    }

    public function testWithASingleAddedFileButNoViolation()
    {
        $file = $this->file('test.php');
        $diff = $this->diff([$file]);

        $this->phpstan->expects($this->once())
            ->method('execute')
            ->with('test.php')
            ->will($this->returnValue(new \ArrayIterator([])));

        $violations = iterator_to_array($this->inspection->inspectDiff($diff));

        $this->assertEmpty($violations);
    }

    public function testWithASingleAddedFileAndSomeViolations()
    {
        $file = $this->file('test.php');
        $diff = $this->diff([$file]);

        $file->method('findPositionForLine')
            ->will($this->returnCallback(function ($line) {
                if ($line === 12) {
                    throw Model\Exception\LineNotInDiff::line($line);
                }

                return $line + 1;
            }));

        $this->phpstan->expects($this->once())
            ->method('execute')
            ->with('test.php')
            ->will($this->returnValue(new \ArrayIterator([
                // the first one is configured not to be in the diff
                [
                    'line' => 12,
                    'message' => 'some message',
                ],
                [
                    'line' => 20,
                    'message' => 'some other message',
                ],
                [
                    'line' => 24,
                    'message' => 'another message',
                ],
            ])));

        $violations = iterator_to_array($this->inspection->inspectDiff($diff));

        $this->assertCount(2, $violations);

        /** @var Entity\Inspection\Violation $firstViolation */
        $firstViolation = $violations[0];
        $this->assertInstanceOf(Entity\Inspection\Violation::class, $firstViolation);
        $this->assertEquals(Entity\Inspection\Violation::ERROR, $firstViolation->getSeverity());
        $this->assertFalse($firstViolation->isWarning());
        $this->assertTrue($firstViolation->isError());
        $this->assertEquals('test.php', $firstViolation->getFile());
        $this->assertEquals('some other message', $firstViolation->getDescription());
        $this->assertEquals(20 + 1, $firstViolation->getPosition());

        /** @var Entity\Inspection\Violation $secondViolation */
        $secondViolation = $violations[1];
        $this->assertInstanceOf(Entity\Inspection\Violation::class, $secondViolation);
        $this->assertEquals(Entity\Inspection\Violation::ERROR, $secondViolation->getSeverity());
        $this->assertFalse($secondViolation->isWarning());
        $this->assertTrue($secondViolation->isError());
        $this->assertEquals('test.php', $secondViolation->getFile());
        $this->assertEquals('another message', $secondViolation->getDescription());
        $this->assertEquals(24 + 1, $secondViolation->getPosition());
    }
}
