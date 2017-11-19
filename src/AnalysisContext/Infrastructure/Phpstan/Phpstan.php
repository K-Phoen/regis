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

namespace Regis\AnalysisContext\Infrastructure\Phpstan;

use Regis\AnalysisContext\Application\Inspection\PhpstanRunner;
use Symfony\Component\Process\Process;

class Phpstan implements PhpstanRunner
{
    private $phpstanBin;

    public function __construct(string $phpCsBin)
    {
        $this->phpstanBin = $phpCsBin;
    }

    public function execute(string $fileName): \Traversable
    {
        $process = new Process(sprintf(
            '%s analyse --no-progress --level=7 --errorFormat=checkstyle %s',
            escapeshellarg($this->phpstanBin),
            escapeshellarg($fileName)
        ));
        $process->run();

        yield from $this->processResults($fileName, $process->getOutput());
    }

    private function processResults(string $originalFileName, string $xmlReport): \Traversable
    {
        $xml = new \SimpleXMLElement($xmlReport);

        /** @var \SimpleXMLElement $file */
        foreach ($xml->file as $file) {
            /** @var \SimpleXMLElement $violation */
            foreach ($file->error as $violation) {
                yield [
                    'file' => $originalFileName,
                    'line' => (int) (string) $violation['line'],
                    'column' => (int) (string) $violation['column'],
                    'severity' => (string) $violation['severity'],
                    'message' => (string) $violation['message'],
                ];
            }
        }
    }
}
