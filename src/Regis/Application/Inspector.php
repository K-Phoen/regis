<?php

declare(strict_types=1);

namespace Regis\Application;

use Regis\Application\Vcs\Repository;
use Regis\Domain\Entity;
use Regis\Domain\Model;
use Regis\Infrastructure\Vcs\Git;

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

    public function inspect(Model\Git\Repository $repository, Model\Git\Revisions $revisions): Entity\Inspection\Report
    {
        $gitRepository = $this->git->getRepository($repository);
        $gitRepository->checkout($revisions->getHead());

        $diff = $gitRepository->getDiff($revisions);

        return $this->inspectDiff($gitRepository, $diff);
    }

    private function inspectDiff(Repository $repository, Model\Git\Diff $diff): Entity\Inspection\Report
    {
        $report = new Entity\Inspection\Report($diff->getRawDiff());

        foreach ($this->inspections as $inspection) {
            $analysis = new Entity\Inspection\Analysis($inspection->getType());

            foreach ($inspection->inspectDiff($repository, $diff) as $violation) {
                $analysis->addViolation($violation);
            }

            $report->addAnalysis($analysis);
        }

        return $report;
    }
}
