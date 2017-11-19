<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\Event;

use Regis\BitbucketContext\Domain\Model\PullRequest;

class PullRequestOpened
{
    private $pullRequest;

    public function __construct(PullRequest $pullRequest)
    {
        $this->pullRequest = $pullRequest;
    }

    public function getPullRequest(): PullRequest
    {
        return $this->pullRequest;
    }
}
