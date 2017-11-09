<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\CommandHandler\Inspection;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\Reporter;

class SendViolationsAsComments
{
    private $reporter;

    public function __construct(Reporter $reporter)
    {
        $this->reporter = $reporter;
    }

    public function handle(Command\Inspection\SendViolationsAsComments $command)
    {
        $inspection = $command->getInspection();
        $repository = $inspection->getRepository();

        if (!$inspection->hasReport()) {
            return;
        }

        $pullRequest = $inspection->getPullRequest();

        foreach ($inspection->getReport()->violations() as $violation) {
            $this->reporter->report($repository, $violation, $pullRequest);
        }
    }
}
