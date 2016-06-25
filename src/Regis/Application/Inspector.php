<?php

declare(strict_types=1);

namespace Regis\Application;

use Gitonomy\Git as Gitonomy;
use Regis\Vcs\Git;

class Inspector
{
    private $git;
    private $reporter;
    /** @var Inspection[] */
    private $inspections;

    public function __construct(Git $git, Reporter $reporter, array $inspections = [])
    {
        $this->git = $git;
        $this->reporter = $reporter;
        $this->inspections = $inspections;
    }

    public function inspect(Model\Github\PullRequest $pullRequest)
    {
        $repository = $pullRequest->getRepository();
        $revisions = $pullRequest->getRevisions();

        $gitRepository = $this->git->getRepository($repository);
        $gitRepository->update();

        $diff = $gitRepository->getDiff($revisions);

        return $this->inspectDiff($pullRequest, $diff);
    }

    private function inspectDiff(Model\Github\PullRequest $pullRequest, Model\Git\Diff $diff): ReportSummary
    {
        $report = new ReportSummary();

        foreach ($this->inspections as $inspection) {
            foreach ($inspection->inspectDiff($diff) as $violation) {
                $report->newViolation($violation);
                $this->reporter->report($violation, $pullRequest);
            }
        }

        return $report;
    }
}