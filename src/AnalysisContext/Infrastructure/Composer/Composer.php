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
use Symfony\Component\Process\Process;

class Composer implements ComposerRunner
{
    private const INSTALL_TIMEOUT = 4 * 60; // 4 minutes in seconds

    private $composerBin;

    public function __construct(string $composerBin)
    {
        $this->composerBin = $composerBin;
    }

    public function install(string $workingDirectory): void
    {
        // TODO do nothing if there is no composer.json file

        $process = new Process(sprintf(
            '%s install -o --ignore-platform-reqs --no-interaction',
            escapeshellarg($this->composerBin)
        ));
        $process->setTimeout(self::INSTALL_TIMEOUT);
        $process->setWorkingDirectory($workingDirectory);

        $process->run();
    }
}
