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
use Symfony\Component\Process\Process;

class CodeSniffer implements CodeSnifferRunner
{
    private $phpcsBin;

    public function __construct(string $phpCsBin)
    {
        $this->phpcsBin = $phpCsBin;
    }

    public function execute(string $fileName, string $fileContent, string $standards): array
    {
        $process = new Process(sprintf(
            '%s --standard=%s --report=json --stdin-path=%s',
            escapeshellarg($this->phpcsBin),
            $standards,
            escapeshellarg($fileName)
        ));

        $process->setInput($fileContent);
        $process->run();

        return json_decode($process->getOutput(), true);
    }
}
