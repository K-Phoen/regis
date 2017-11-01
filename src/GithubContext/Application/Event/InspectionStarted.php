<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Event;

use Regis\GithubContext\Application\Event;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Model;

class InspectionStarted implements Event
{
    private $inspection;
    private $pullRequest;

    public function __construct(Entity\Inspection $inspection, Model\PullRequest $pullRequest)
    {
        $this->inspection = $inspection;
        $this->pullRequest = $pullRequest;
    }

    public function getInspection(): Entity\Inspection
    {
        return $this->inspection;
    }

    public function getPullRequest(): Model\PullRequest
    {
        return $this->pullRequest;
    }

    public function getEventName(): string
    {
        return Event::INSPECTION_STARTED;
    }
}
