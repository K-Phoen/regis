<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Application\CommandHandler;

use Regis\AnalysisContext\Application\Command;
use Regis\AnalysisContext\Application\Inspector;
use Regis\AnalysisContext\Domain\Model\Inspection\Report;

class InspectRevisions
{
    private $inspector;

    public function __construct(Inspector $inspector)
    {
        $this->inspector = $inspector;
    }

    public function handle(Command\InspectRevisions $command): Report
    {
        return $this->inspector->inspect($command->getRepository(), $command->getRevisions());
    }
}
