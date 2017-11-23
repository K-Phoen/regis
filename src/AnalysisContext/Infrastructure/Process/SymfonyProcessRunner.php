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

namespace Regis\AnalysisContext\Infrastructure\Process;

use Psr\Log\LoggerInterface;
use Regis\AnalysisContext\Application\Process\Env;
use Regis\AnalysisContext\Application\Process\Runner as ProcessRunner;
use Symfony\Component\Process\Process;

class SymfonyProcessRunner implements ProcessRunner
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function run(string $command, array $args, Env $env): string
    {
        $escapedArgs = array_map('escapeshellarg', $args);
        $commandLine = sprintf('%s %s', $command, implode(' ', $escapedArgs));

        $this->logger->debug('Running command {command_line}', [
            'command_line' => $commandLine,
            'working_dir' => $env->workingDir(),
            'timeout' => $env->timeout(),
        ]);

        $process = new Process($commandLine);

        $process->setWorkingDirectory($env->workingDir());
        $process->setTimeout($env->timeout());

        $process->run();

        // TODO logs, failures?

        return $process->getOutput();
    }
}
