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

namespace Regis\AnalysisContext\Infrastructure\CodeSniffer;

use Regis\AnalysisContext\Application\Inspection\CodeSnifferRunner;
use Regis\AnalysisContext\Application\Process\Runner as ProcessRunner;
use Regis\AnalysisContext\Application\Process\Env;

class CodeSniffer implements CodeSnifferRunner
{
    private $processRunner;
    private $phpcsBin;

    public function __construct(ProcessRunner $processRunner, string $phpCsBin)
    {
        $this->processRunner = $processRunner;
        $this->phpcsBin = $phpCsBin;
    }

    public function execute(Env $env, string $fileName, string $standards): iterable
    {
        $result = $this->processRunner->run($this->phpcsBin, [
            '--standard='.$standards,
            '--report=json',
            $fileName,
        ], $env);

        return json_decode($result, true);
    }
}
