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

namespace Regis\AnalysisContext\Infrastructure\Composer;

use Regis\AnalysisContext\Application\Composer as ComposerRunner;
use Regis\AnalysisContext\Application\Process\Env;
use Regis\AnalysisContext\Application\Process\Runner;
use Symfony\Component\Filesystem\Filesystem;

class Composer implements ComposerRunner
{
    private const INSTALL_TIMEOUT = 4 * 60; // 4 minutes in seconds

    private $composerBin;
    private $processRunner;
    private $fs;

    public function __construct(Runner $processRunner, string $composerBin, Filesystem $filesystem = null)
    {
        $this->processRunner = $processRunner;
        $this->composerBin = $composerBin;
        $this->fs = $filesystem ?: new Filesystem();
    }

    public function install(string $workingDirectory): void
    {
        if (!$this->fs->exists($workingDirectory.'/composer.json')) {
            return;
        }

        $this->processRunner->run($this->composerBin, [
            'install',
            '-o',
            '--ignore-platform-reqs',
            '--no-interaction',
        ], new Env($workingDirectory, self::INSTALL_TIMEOUT));
    }
}
