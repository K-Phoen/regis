<?php

declare(strict_types=1);

namespace Regis\Application\Command\Inspection;

use Regis\Application\Entity\Inspection;
use Regis\Application\Model\Github\PullRequest;

class Run
{
    private $inspection;
    private $pullRequest;

    public function __construct(Inspection $inspection, PullRequest $pullRequest)
    {
        $this->inspection = $inspection;
        $this->pullRequest = $pullRequest;
    }

    public function getInspection(): Inspection
    {
        return $this->inspection;
    }

    public function getPullRequest(): PullRequest
    {
        return $this->pullRequest;
    }
}