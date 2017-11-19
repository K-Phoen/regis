<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\Event;

use Regis\BitbucketContext\Domain\Entity\PullRequestInspection;

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
