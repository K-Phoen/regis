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

namespace Regis\AnalysisContext\Application\Inspection;

use Regis\AnalysisContext\Application\Inspection;
use Regis\AnalysisContext\Application\Vcs;
use Regis\AnalysisContext\Domain\Model\Exception\LineNotInDiff;
use Regis\AnalysisContext\Domain\Model\Git as Model;
use Regis\AnalysisContext\Domain\Entity\Violation;

class CodeSniffer implements Inspection
{
    public const CONFIG_FILE = 'phpcs.xml';

    private $codeSniffer;
    private $config;

    public function __construct(CodeSnifferRunner $codeSniffer, array $codeSnifferConfig = [])
    {
        $this->codeSniffer = $codeSniffer;
        $this->config = $codeSnifferConfig;
    }

    public function getType(): string
    {
        return 'phpcs';
    }

    public function inspectDiff(Vcs\Repository $repository, Model\Diff $diff): \Traversable
    {
        try {
            $standards = $this->locateRuleset($repository);
        } catch (Exception\ConfigurationNotFound $e) {
            $standards = implode(',', $this->config['standards']);
        }

        /** @var Model\Diff\File $file */
        foreach ($diff->getAddedPhpFiles() as $file) {
            $report = $this->codeSniffer->execute($file->getNewName(), $file->getNewContent(), $standards);

            foreach ($report['files'] as $fileReport) {
                yield from $this->buildViolations($file, $fileReport);
            }
        }
    }

    private function locateRuleset(Vcs\Repository $repository): string
    {
        try {
            return $repository->locateFile(self::CONFIG_FILE);
        } catch (Vcs\FileNotFound $e) {
            throw new Exception\ConfigurationNotFound($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function buildViolations(Model\Diff\File $file, array $report): \Traversable
    {
        foreach ($report['messages'] as $message) {
            try {
                $position = $file->findPositionForLine($message['line']);
            } catch (LineNotInDiff $e) {
                continue;
            }

            if ($message['type'] === 'ERROR') {
                yield Violation::newError($file->getNewName(), $message['line'], $position, $message['message']);
            } else {
                yield Violation::newWarning($file->getNewName(), $message['line'], $position, $message['message']);
            }
        }
    }
}
