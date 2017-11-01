<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application;

use Regis\AnalysisContext\Application\Vcs\Git;
use Regis\AnalysisContext\Application\Vcs\Repository;
use Regis\AnalysisContext\Domain\Model;

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

    public function inspect(Model\Git\Repository $repository, Model\Git\Revisions $revisions): Model\Inspection\Report
    {
        $gitRepository = $this->git->getRepository($repository);
        $gitRepository->checkout($revisions->getHead());

        $diff = $gitRepository->getDiff($revisions);

        return $this->inspectDiff($gitRepository, $diff);
    }

    private function inspectDiff(Repository $repository, Model\Git\Diff $diff): Model\Inspection\Report
    {
        $report = new Model\Inspection\Report($diff->getRawDiff());

        foreach ($this->inspections as $inspection) {
            $analysis = new Model\Inspection\Analysis($inspection->getType());

            foreach ($inspection->inspectDiff($repository, $diff) as $violation) {
                $analysis->addViolation($violation);
            }

            $report->addAnalysis($analysis);
        }

        return $report;
    }
}
