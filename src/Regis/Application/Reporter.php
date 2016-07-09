<?php

declare(strict_types=1);

namespace Regis\Application;

use Regis\Domain\Entity;
use Regis\Domain\Model;

interface Reporter
{
    function report(Entity\Repository $repository, Entity\Inspection\Violation $violation, Model\Github\PullRequest $pullRequest);
}
