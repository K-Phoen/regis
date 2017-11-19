<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\CommandHandler\Inspection;

use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Application\Inspection\ViolationsCache;

class ClearViolationsCache
{
    private $violationsCache;

    public function __construct(ViolationsCache $violationsCache)
    {
        $this->violationsCache = $violationsCache;
    }

    public function handle(Command\Inspection\ClearViolationsCache $command)
    {
        $this->violationsCache->clear($command->getPullRequest());
    }
}
