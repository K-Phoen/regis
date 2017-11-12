<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Event;

use Regis\GithubContext\Domain\Entity\PullRequestInspection;

class InspectionStarted
{
    private $inspection;

    public function __construct(PullRequestInspection $inspection)
    {
        $this->inspection = $inspection;
    }

    public function getInspection(): PullRequestInspection
    {
        return $this->inspection;
    }
}
