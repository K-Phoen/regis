<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application;

use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Model;

interface Reporter
{
    public function report(Entity\Repository $repository, Entity\Inspection\Violation $violation, Model\PullRequest $pullRequest);
}
