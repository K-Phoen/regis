<?php

namespace Tests\Regis\AnalysisContext\Application\Inspection;

use Regis\AnalysisContext\Application\Inspection\CodeSniffer;
use Regis\AnalysisContext\Application\Inspection\CodeSnifferRunner;
use Regis\AnalysisContext\Application\Vcs\Repository;
use Regis\AnalysisContext\Domain\Entity;
use Regis\AnalysisContext\Domain\Model\Exception\LineNotInDiff;

class CodeSnifferTest extends InspectionTestCase
{
    private $codeSniffer;
    private $vcsRepository;

    /** @var CodeSniffer */
    private $inspection;

    public function setUp()
    {
        $this->codeSniffer = $this->createMock(CodeSnifferRunner::class);
        $this->vcsRepository = $this->createMock(Repository::class);

        $this->inspection = new CodeSniffer($this->codeSniffer);
    }

    public function testItHasAType()
    {
        $this->assertEquals('phpcs', $this->inspection->getType());
    }

    public function testWithNoAddedFiles()
    {
        $this->codeSniffer->expects($this->never())->method('execute');

        $violations = iterator_to_array($this->inspection->inspectDiff($this->vcsRepository, $this->diff()));

        $this->assertEmpty($violations);
    }

    public function testWithASingleAddedFileButNoViolation()
    {
        $file = $this->file('test.php');
        $diff = $this->diff([$file]);

        $this->codeSniffer->expects($this->once())
            ->method('execute')
            ->with('test.php', $this->anything())
            ->willReturn([
                'files' => [],
            ]);

        $violations = iterator_to_array($this->inspection->inspectDiff($this->vcsRepository, $diff));

        $this->assertEmpty($violations);
    }

    public function testWithASingleAddedFileAndSomeViolations()
    {
        $file = $this->file('test.php');
        $diff = $this->diff([$file]);

        $file->method('findPositionForLine')
            ->will($this->returnCallback(function ($line) {
                if ($line === 12) {
                    throw LineNotInDiff::line($line);
                }

                return $line + 1;
            }));

        $this->codeSniffer->expects($this->once())
            ->method('execute')
            ->with('test.php', $this->anything())
            ->willReturn([
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
                        ],
                    ],
                ],
            ]);

        $violations = iterator_to_array($this->inspection->inspectDiff($this->vcsRepository, $diff));

        $this->assertCount(2, $violations);

        /** @var Entity\Violation $firstViolation */
        $firstViolation = $violations[0];
        $this->assertInstanceOf(Entity\Violation::class, $firstViolation);
        $this->assertEquals(Entity\Violation::WARNING, $firstViolation->severity());
        $this->assertTrue($firstViolation->isWarning());
        $this->assertFalse($firstViolation->isError());
        $this->assertEquals('test.php', $firstViolation->file());
        $this->assertEquals('some warning message', $firstViolation->description());
        $this->assertEquals(24 + 1, $firstViolation->position());

        /** @var Entity\Violation $secondViolation */
        $secondViolation = $violations[1];
        $this->assertInstanceOf(Entity\Violation::class, $secondViolation);
        $this->assertEquals(Entity\Violation::ERROR, $secondViolation->severity());
        $this->assertFalse($secondViolation->isWarning());
        $this->assertTrue($secondViolation->isError());
        $this->assertEquals('test.php', $secondViolation->file());
        $this->assertEquals('some error message', $secondViolation->description());
        $this->assertEquals(42 + 1, $secondViolation->position());
    }
}
