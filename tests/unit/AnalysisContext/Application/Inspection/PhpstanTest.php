<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

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
        $this->assertSame('phpstan', $this->inspection->getType());
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
        $this->assertSame(Entity\Violation::ERROR, $firstViolation->severity());
        $this->assertFalse($firstViolation->isWarning());
        $this->assertTrue($firstViolation->isError());
        $this->assertSame('test.php', $firstViolation->file());
        $this->assertSame('some other message', $firstViolation->description());
        $this->assertSame(20 + 1, $firstViolation->position());

        /** @var Entity\Violation $secondViolation */
        $secondViolation = $violations[1];
        $this->assertInstanceOf(Entity\Violation::class, $secondViolation);
        $this->assertSame(Entity\Violation::ERROR, $secondViolation->severity());
        $this->assertFalse($secondViolation->isWarning());
        $this->assertTrue($secondViolation->isError());
        $this->assertSame('test.php', $secondViolation->file());
        $this->assertSame('another message', $secondViolation->description());
        $this->assertSame(24 + 1, $secondViolation->position());
    }
}
