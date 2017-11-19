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

use Regis\AnalysisContext\Application\Inspection\PhpMd;
use Regis\AnalysisContext\Application\Inspection\PhpMdRunner;
use Regis\AnalysisContext\Application\Vcs;
use Regis\AnalysisContext\Application\Vcs\Repository;
use Regis\AnalysisContext\Domain\Entity;
use Regis\AnalysisContext\Domain\Model\Exception\LineNotInDiff;

class PhpMdTest extends InspectionTestCase
{
    const DEFAULT_RULESETS = ['first', 'other'];

    private $phpMd;
    private $vcsRepository;

    /** @var PhpMd */
    private $inspection;

    public function setUp()
    {
        $this->phpMd = $this->getMockBuilder(PhpMdRunner::class)->getMock();
        $this->vcsRepository = $this->createMock(Repository::class);

        $this->inspection = new PhpMd($this->phpMd, [
            'rulesets' => self::DEFAULT_RULESETS,
        ]);
    }

    public function testItHasAType()
    {
        $this->assertSame('phpmd', $this->inspection->getType());
    }

    public function testWithNoAddedFiles()
    {
        $this->phpMd->expects($this->never())->method('execute');

        $violations = iterator_to_array($this->inspection->inspectDiff($this->vcsRepository, $this->diff()));

        $this->assertEmpty($violations);
    }

    public function testItUsesTheDefaultRulesetIfNoneIsFoundInTheRepository()
    {
        $file = $this->file('test.php');
        $diff = $this->diff([$file]);

        $this->vcsRepository->expects($this->once())
            ->method('locateFile')
            ->with(PhpMd::CONFIG_FILE)
            ->willThrowException(new Vcs\FileNotFound());

        $this->phpMd->expects($this->once())
            ->method('execute')
            ->with('test.php', $this->anything(), implode(',', self::DEFAULT_RULESETS))
            ->willReturn(new \ArrayIterator());

        $violations = iterator_to_array($this->inspection->inspectDiff($this->vcsRepository, $diff));

        $this->assertEmpty($violations);
    }

    public function testItUsesTheConfigFileFromTheRepositoryWhenItExists()
    {
        $file = $this->file('test.php');
        $diff = $this->diff([$file]);

        $this->vcsRepository->expects($this->once())
            ->method('locateFile')
            ->with(PhpMd::CONFIG_FILE)
            ->willReturn($configPath = 'config-file-path.xml');

        $this->phpMd->expects($this->once())
            ->method('execute')
            ->with('test.php', $this->anything(), $configPath)
            ->willReturn(new \ArrayIterator());

        $violations = iterator_to_array($this->inspection->inspectDiff($this->vcsRepository, $diff));

        $this->assertEmpty($violations);
    }

    public function testWithASingleAddedFileButNoViolation()
    {
        $file = $this->file('test.php');
        $diff = $this->diff([$file]);

        $this->phpMd->expects($this->once())
            ->method('execute')
            ->with('test.php', $this->anything())
            ->willReturn(new \ArrayIterator());

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

        $this->phpMd->expects($this->once())
            ->method('execute')
            ->with('test.php', $this->anything())
            ->willReturn(new \ArrayIterator([
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
            ]));

        $violations = iterator_to_array($this->inspection->inspectDiff($this->vcsRepository, $diff));

        $this->assertCount(2, $violations);

        /** @var Entity\Violation $firstViolation */
        $firstViolation = $violations[0];
        $this->assertInstanceOf(Entity\Violation::class, $firstViolation);
        $this->assertSame(Entity\Violation::WARNING, $firstViolation->severity());
        $this->assertTrue($firstViolation->isWarning());
        $this->assertFalse($firstViolation->isError());
        $this->assertSame('test.php', $firstViolation->file());
        $this->assertSame('some warning message', $firstViolation->description());
        $this->assertSame(24 + 1, $firstViolation->position());

        /** @var Entity\Violation $secondViolation */
        $secondViolation = $violations[1];
        $this->assertInstanceOf(Entity\Violation::class, $secondViolation);
        $this->assertSame(Entity\Violation::ERROR, $secondViolation->severity());
        $this->assertFalse($secondViolation->isWarning());
        $this->assertTrue($secondViolation->isError());
        $this->assertSame('test.php', $secondViolation->file());
        $this->assertSame('some error message', $secondViolation->description());
        $this->assertSame(42 + 1, $secondViolation->position());
    }
}
