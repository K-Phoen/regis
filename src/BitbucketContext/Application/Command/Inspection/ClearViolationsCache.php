<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\Command\Inspection;

use Regis\BitbucketContext\Domain\Model;

class ClearViolationsCache
{
    private $pullRequest;

    public function __construct(Model\PullRequest $pullRequest)
    {
        $this->pullRequest = $pullRequest;
    }

    public function getPullRequest(): Model\PullRequest
    {
        return $this->pullRequest;
    }
}
