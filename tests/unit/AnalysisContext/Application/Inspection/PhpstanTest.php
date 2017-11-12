<?php

namespace Tests\Regis\AnalysisContext\Application\Inspection;

use Regis\AnalysisContext\Application\Inspection\Phpstan;
use Regis\AnalysisContext\Application\Inspection\PhpstanRunner;
use Regis\AnalysisContext\Application\Vcs\Repository;
use Regis\AnalysisContext\Domain\Entity;
use Regis\AnalysisContext\Domain\Model\Exception\LineNotInDiff;

class PhpstanTest extends InspectionTestCase
{
    private $phpstan;
    private $vcsRepository;

    /** @var Phpstan */
    private $inspection;

    public function setUp()
    {
        $this->phpstan = $this->createMock(PhpstanRunner::class);
        $this->vcsRepository = $this->createMock(Repository::class);

        $this->inspection = new Phpstan($this->phpstan);
    }

    public function testItHasAType()
    {
        $this->assertEquals('phpstan', $this->inspection->getType());
    }

    public function testWithNoAddedFiles()
    {
        $this->phpstan->expects($this->never())->method('execute');

        $violations = iterator_to_array($this->inspection->inspectDiff($this->vcsRepository, $this->diff()));

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

        $violations = iterator_to_array($this->inspection->inspectDiff($this->vcsRepository, $diff));

        $this->assertCount(2, $violations);

        /** @var Entity\Violation $firstViolation */
        $firstViolation = $violations[0];
        $this->assertInstanceOf(Entity\Violation::class, $firstViolation);
        $this->assertEquals(Entity\Violation::ERROR, $firstViolation->severity());
        $this->assertFalse($firstViolation->isWarning());
        $this->assertTrue($firstViolation->isError());
        $this->assertEquals('test.php', $firstViolation->file());
        $this->assertEquals('some other message', $firstViolation->description());
        $this->assertEquals(20 + 1, $firstViolation->position());

        /** @var Entity\Violation $secondViolation */
        $secondViolation = $violations[1];
        $this->assertInstanceOf(Entity\Violation::class, $secondViolation);
        $this->assertEquals(Entity\Violation::ERROR, $secondViolation->severity());
        $this->assertFalse($secondViolation->isWarning());
        $this->assertTrue($secondViolation->isError());
        $this->assertEquals('test.php', $secondViolation->file());
        $this->assertEquals('another message', $secondViolation->description());
        $this->assertEquals(24 + 1, $secondViolation->position());
    }
}
