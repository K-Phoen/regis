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
use Regis\AnalysisContext\Application\Process\Env;
use Regis\AnalysisContext\Domain\Model\Exception\LineNotInDiff;
use Regis\AnalysisContext\Domain\Model\Git as Model;
use Regis\AnalysisContext\Domain\Entity\Violation;

class Phpstan implements Inspection
{
    public const CONFIG_FILE = 'phpstan.neon';

    private $phpstan;

    public function __construct(PhpstanRunner $phpstan)
    {
        $this->phpstan = $phpstan;
    }

    public function getType(): string
    {
        return 'phpstan';
    }

    public function inspectDiff(Vcs\Repository $repository, Model\Diff $diff): \Traversable
    {
        try {
            $configFile = $this->locateConfigFile($repository);
        } catch (Exception\ConfigurationNotFound $e) {
            $configFile = null;
        }

        $runnerEnv = new Env($repository->root());

        /** @var Model\Diff\File $file */
        foreach ($diff->getAddedPhpFiles() as $file) {
            $report = $this->phpstan->execute($runnerEnv, $file->getNewName(), $configFile);

            foreach ($report as $entry) {
                try {
                    yield $this->buildViolation($file, $entry);
                } catch (LineNotInDiff $e) {
                    continue;
                }
            }
        }
    }

    private function locateConfigFile(Vcs\Repository $repository): string
    {
        try {
            return $repository->locateFile(self::CONFIG_FILE);
        } catch (Vcs\FileNotFound $e) {
            throw new Exception\ConfigurationNotFound($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function buildViolation(Model\Diff\File $file, array $report): Violation
    {
        $position = $file->findPositionForLine($report['line']);

        return Violation::newError($file->getNewName(), $report['line'], $position, $report['message']);
    }
}
