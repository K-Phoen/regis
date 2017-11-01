<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\CommandHandler\Inspection;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Repository;

class SavePullRequestReport
{
    private $inspectionsRepository;

    public function __construct(Repository\Inspections $inspectionsRepository)
    {
        $this->inspectionsRepository = $inspectionsRepository;
    }

    public function handle(Command\Inspection\SavePullRequestReport $command)
    {
        $inspection = $command->getInspection();

        $inspection->setReport($command->getReport());

        $this->inspectionsRepository->save($inspection);
    }
}
