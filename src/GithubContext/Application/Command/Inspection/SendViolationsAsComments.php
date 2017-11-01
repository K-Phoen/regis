<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Command\Inspection;

use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Model;

class SendViolationsAsComments
{
    private $inspection;
    private $pullRequest;

    public function __construct(Entity\PullRequestInspection $inspection, Model\PullRequest $pullRequest)
    {
        $this->inspection = $inspection;
        $this->pullRequest = $pullRequest;
    }

    public function getInspection(): Entity\PullRequestInspection
    {
        return $this->inspection;
    }

    public function getPullRequest(): Model\PullRequest
    {
        return $this->pullRequest;
    }
}
