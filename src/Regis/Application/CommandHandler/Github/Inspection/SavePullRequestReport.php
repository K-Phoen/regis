<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Github\Inspection;

use Regis\Application\Command;
use Regis\Domain\Repository;

class SavePullRequestReport
{
    private $inspectionsRepository;

    public function __construct(Repository\Inspections $inspectionsRepository)
    {
        $this->inspectionsRepository = $inspectionsRepository;
    }

    public function handle(Command\Github\Inspection\SavePullRequestReport $command)
    {
        $inspection = $command->getInspection();

        $inspection->setReport($command->getReport());

        $this->inspectionsRepository->save($inspection);
    }
}