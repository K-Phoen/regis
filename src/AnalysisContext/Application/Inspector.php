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

namespace Regis\AnalysisContext\Application;

use Regis\AnalysisContext\Application\Vcs\Git;
use Regis\AnalysisContext\Application\Vcs\Repository;
use Regis\AnalysisContext\Domain\Model;
use Regis\AnalysisContext\Domain\Entity;

class Inspector
{
    private $git;
    /** @var Inspection[] */
    private $inspections;

    public function __construct(Git $git, array $inspections = [])
    {
        $this->git = $git;
        $this->inspections = $inspections;
    }

    public function inspect(Model\Git\Repository $repository, Model\Git\Revisions $revisions): Entity\Report
    {
        $gitRepository = $this->git->getRepository($repository);
        $gitRepository->checkout($revisions->getHead());

        $diff = $gitRepository->getDiff($revisions);

        return $this->inspectDiff($gitRepository, $diff);
    }

    private function inspectDiff(Repository $repository, Model\Git\Diff $diff): Entity\Report
    {
        $report = new Entity\Report($diff->getRawDiff());

        foreach ($this->inspections as $inspection) {
            $analysis = new Entity\Analysis($report, $inspection->getType());

            foreach ($inspection->inspectDiff($repository, $diff) as $violation) {
                $analysis->addViolation($violation);
            }

            $report->addAnalysis($analysis);
        }

        return $report;
    }
}
