<?php

declare(strict_types=1);

namespace Regis\Domain;

use Regis\Domain\Model;

interface Reporter
{
    function report(Model\Violation $violation, Model\Github\PullRequest $pullRequest);
}