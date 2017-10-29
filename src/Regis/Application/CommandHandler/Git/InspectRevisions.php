<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Git;

use Regis\Application\Command;
use Regis\Application\Inspector;

class InspectRevisions
{
    private $inspector;

    public function __construct(Inspector $inspector)
    {
        $this->inspector = $inspector;
    }

    public function handle(Command\Git\InspectRevisions $command)
    {
        return $this->inspector->inspect($command->getRepository(), $command->getRevisions());
    }
}
