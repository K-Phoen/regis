<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Github\Inspection;

use Regis\Application\Command;
use Regis\Application\Reporter;

class SendViolationsAsComments
{
    private $reporter;

    public function __construct(Reporter $reporter)
    {
        $this->reporter = $reporter;
    }

    public function handle(Command\Github\Inspection\SendViolationsAsComments $command)
    {
        $inspection = $command->getInspection();

        foreach ($inspection->getReport()->getViolations() as $violation) {
            $this->reporter->report($violation, $command->getPullRequest());
        }
    }
}