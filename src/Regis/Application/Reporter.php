<?php

declare(strict_types=1);

namespace Regis\Application;

use Regis\Application\Entity;
use Regis\Application\Model;

interface Reporter
{
    function report(Entity\Inspection\Violation $violation, Model\Github\PullRequest $pullRequest);
}