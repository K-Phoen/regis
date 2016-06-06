<?php

declare(strict_types=1);

namespace Regis\Application;

use Regis\Application\Model;

interface Reporter
{
    function report(Model\Violation $violation, Model\Github\PullRequest $pullRequest);
}