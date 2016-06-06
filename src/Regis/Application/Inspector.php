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
        $gitRepository = $this->git->getRepository($pullRequest->getRepository());
        $gitRepository->update();

        $diff = $gitRepository->getDiff($pullRequest->getBase(), $pullRequest->getHead());
        $this->inspectDiff($pullRequest, $diff);
    }

    private function inspectDiff(Model\Github\PullRequest $pullRequest, Model\Git\Diff $diff)
    {
        foreach ($this->inspections as $inspection) {
            foreach ($inspection->inspectDiff($diff) as $violation) {
                $this->reporter->report($violation, $pullRequest);
            }
        }
    }
}