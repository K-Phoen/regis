<?php

declare(strict_types=1);

namespace Regis\Application\Command\Github\Inspection;

use Regis\Domain\Entity;
use Regis\Domain\Model;

class SendViolationsAsComments
{
    private $inspection;
    private $pullRequest;

    public function __construct(Entity\Github\PullRequestInspection $inspection, Model\Github\PullRequest $pullRequest)
    {
        $this->inspection = $inspection;
        $this->pullRequest = $pullRequest;
    }

    public function getInspection(): Entity\Github\PullRequestInspection
    {
        return $this->inspection;
    }

    public function getPullRequest(): Model\Github\PullRequest
    {
        return $this->pullRequest;
    }
}
