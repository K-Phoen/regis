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

namespace Regis\AnalysisContext\Infrastructure\PhpMd;

use Regis\AnalysisContext\Application\Inspection\PhpMdRunner;
use Symfony\Component\Process\Process;

class PhpMd implements PhpMdRunner
{
    private $phpmdBin;
    private $tempDir;

    public function __construct(string $phpCsBin, string $tempDir = null)
    {
        $this->phpmdBin = $phpCsBin;
        $this->tempDir = $tempDir ?: sys_get_temp_dir();
    }

    public function execute(string $fileName, string $fileContent, string $ruleset): \Traversable
    {
        $tempFile = sprintf('%s/%s', $this->tempDir, uniqid('phpmd_', true).str_replace('/', '', $fileName));

        file_put_contents($tempFile, $fileContent);

        try {
            $process = new Process(sprintf(
                '%s %s xml %s',
                escapeshellarg($this->phpmdBin),
                escapeshellarg($tempFile),
                escapeshellarg($ruleset)
            ));

            $process->run();
        } finally {
            unlink($tempFile);
        }

        yield from $this->processResults($fileName, $process->getOutput());
    }

    private function processResults(string $originalFileName, string $xmlReport): \Traversable
    {
        $xml = new \SimpleXMLElement($xmlReport);

        /** @var \SimpleXMLElement $file */
        foreach ($xml->file as $file) {
            /** @var \SimpleXMLElement $violation */
            foreach ($file->violation as $violation) {
                yield [
                    'file' => $originalFileName,
                    'beginLine' => (int) (string) $violation['beginline'],
                    'endLine' => (int) (string) $violation['endline'],
                    'rule' => (string) $violation['rule'],
                    'ruleSet' => (string) $violation['ruleset'],
                    'externalInfoUrl' => (string) $violation['externalInfoUrl'],
                    'priority' => (int) (string) $violation['priority'],
                    'description' => trim((string) $violation),
                ];
            }
        }
    }
}
