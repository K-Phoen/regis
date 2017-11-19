<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\Reporter;

use Regis\BitbucketContext\Application\Inspection\ViolationsCache;
use Regis\BitbucketContext\Application\Reporter;
use Regis\BitbucketContext\Domain\Entity;
use Regis\BitbucketContext\Domain\Model;

class DuplicationGuard implements Reporter
{
    private $originalReporter;
    private $violationsCache;

    public function __construct(Reporter $originalReporter, ViolationsCache $violationsCache)
    {
        $this->originalReporter = $originalReporter;
        $this->violationsCache = $violationsCache;
    }

    public function report(Entity\Repository $repository, Model\ReviewComment $comment, Model\PullRequest $pullRequest)
    {
        if ($this->violationsCache->has($comment, $pullRequest)) {
            return;
        }

        $this->originalReporter->report($repository, $comment, $pullRequest);

        $this->violationsCache->save($comment, $pullRequest);
    }
}
