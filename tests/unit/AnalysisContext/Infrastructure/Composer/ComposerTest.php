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

namespace Tests\Regis\AnalysisContext\Infrastructure\Composer;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Application\Process\Env;
use Regis\AnalysisContext\Application\Process\Runner;
use Regis\AnalysisContext\Infrastructure\Composer\Composer;
use Symfony\Component\Filesystem\Filesystem;

class ComposerTest extends TestCase
{
    private const COMPOSER_BIN = 'some-bin';

    /** @var Runner */
    private $processRunner;
    /** @var Filesystem */
    private $fs;

    /** @var Composer */
    private $composer;

    public function setUp()
    {
        $this->processRunner = $this->createMock(Runner::class);
        $this->fs = $this->createMock(Filesystem::class);

        $this->composer = new Composer($this->processRunner, self::COMPOSER_BIN, $this->fs);
    }

    public function testItInstallsPackagesIfAComposerjsonIsPresent()
    {
        $this->fs->method('exists')->with('some-working-dir/composer.json')->willReturn(true);

        $this->processRunner->expects($this->once())
            ->method('run')
            ->with(self::COMPOSER_BIN, $this->anything(), $this->isInstanceOf(Env::class));

        $this->composer->install('some-working-dir');
    }

    public function testItDoesNothingIfNoComposerjsonIsFound()
    {
        $this->fs->method('exists')->with('some-working-dir/composer.json')->willReturn(false);

        $this->processRunner->expects($this->never())->method('run');

        $this->composer->install('some-working-dir');
    }
}
