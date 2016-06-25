<?php

namespace Tests\Regis\Application\Inspection;

use Regis\Application\Inspection\CodeSniffer;
use Regis\Application\Model;
use Regis\CodeSniffer\CodeSniffer as CodeSnifferRunner;

class CodeSnifferTest extends \PHPUnit_Framework_TestCase
{
    private $codeSniffer;
    /** @var CodeSniffer */
    private $inspection;

    public function setUp()
    {
        $this->codeSniffer = $this->getMockBuilder(CodeSnifferRunner::class)->disableOriginalConstructor()->getMock();

        $this->inspection = new CodeSniffer($this->codeSniffer);
    }

    public function testWithNoAddedFiles()
    {
        $this->codeSniffer->expects($this->never())->method('execute');

        $violations = iterator_to_array($this->inspection->inspectDiff($this->diff()));

        $this->assertEmpty($violations);
    }

    public function testWithASingleAddedFileButNoViolation()
    {
        $file = $this->file('test.php');
        $diff = $this->diff([$file]);

        $this->codeSniffer->expects($this->once())
            ->method('execute')
            ->with('test.php', $this->anything())
            ->will($this->returnValue([
                'files' => [],
            ]));

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

        $this->codeSniffer->expects($this->once())
            ->method('execute')
            ->with('test.php', $this->anything())
            ->will($this->returnValue([
                'files' => [
                    [
                        'messages' => [
                            // the first one is configured not to be in the diff
                            [
                                'line' => 12,
                                'type' => 'WARNING',
                                'message' => 'some warning message',
                            ],
                            [
                                'line' => 24,
                                'type' => 'WARNING',
                                'message' => 'some warning message',
                            ],
                            [
                                'line' => 42,
                                'type' => 'ERROR',
                                'message' => 'some error message',
                            ],
                        ]
                    ],
                ],
            ]));

        $violations = iterator_to_array($this->inspection->inspectDiff($diff));

        $this->assertCount(2, $violations);

        /** @var Model\Violation $firstViolation */
        $firstViolation = $violations[0];
        $this->assertInstanceOf(Model\Violation::class, $firstViolation);
        $this->assertEquals(Model\Violation::WARNING, $firstViolation->getSeverity());
        $this->assertTrue($firstViolation->isWarning());
        $this->assertFalse($firstViolation->isError());
        $this->assertEquals('test.php', $firstViolation->getFile());
        $this->assertEquals('some warning message', $firstViolation->getDescription());
        $this->assertEquals(24 + 1, $firstViolation->getPosition());

        /** @var Model\Violation $secondViolation */
        $secondViolation = $violations[1];
        $this->assertInstanceOf(Model\Violation::class, $secondViolation);
        $this->assertEquals(Model\Violation::ERROR, $secondViolation->getSeverity());
        $this->assertFalse($secondViolation->isWarning());
        $this->assertTrue($secondViolation->isError());
        $this->assertEquals('test.php', $secondViolation->getFile());
        $this->assertEquals('some error message', $secondViolation->getDescription());
        $this->assertEquals(42 + 1, $secondViolation->getPosition());
    }

    private function diff(array $addedFiles = []): Model\Git\Diff
    {
        $diff = $this->getMockBuilder(Model\Git\Diff::class)->disableOriginalConstructor()->getMock();
        $diff->expects($this->any())
            ->method('getAddedTextFiles')
            ->will($this->returnValue(new \ArrayIterator($addedFiles)));

        return $diff;
    }

    private function file(string $name): Model\Git\Diff\File
    {
        $diff = $this->getMockBuilder(Model\Git\Diff\File::class)->disableOriginalConstructor()->getMock();
        $diff->expects($this->any())
            ->method('getNewName')
            ->will($this->returnValue($name));
        $diff->expects($this->any())
            ->method('getNewContent')
            ->will($this->returnValue('some content'));

        return $diff;
    }
}
