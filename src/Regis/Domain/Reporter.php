<?php

namespace Regis\Domain;

use Regis\Domain\Model;

interface Reporter
{
    function report(Model\Violation $violation, Model\PullRequest $pullRequest);
}