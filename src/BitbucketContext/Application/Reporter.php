<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application;

use Regis\BitbucketContext\Domain\Entity;
use Regis\BitbucketContext\Domain\Model;

interface Reporter
{
    public function report(Entity\Repository $repository, Model\ReviewComment $comment, Model\PullRequest $pullRequest);
}
