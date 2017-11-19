<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\CommandHandler\Inspection;

use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Application\Reporter;
use Regis\BitbucketContext\Domain\Model;

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
            $this->reporter->report($repository, Model\ReviewComment::fromViolation($violation), $pullRequest);
        }
    }
}
