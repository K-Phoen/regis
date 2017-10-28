<?php

declare(strict_types=1);

namespace Regis\Application\Event;

use Regis\Application\Event;
use Regis\Domain\Entity;
use Regis\Domain\Model\Github\PullRequest;

class InspectionStarted implements Event
{
    private $inspection;
    private $pullRequest;

    public function __construct(Entity\Inspection $inspection, PullRequest $pullRequest)
    {
        $this->inspection = $inspection;
        $this->pullRequest = $pullRequest;
    }

    public function getInspection(): Entity\Inspection
    {
        return $this->inspection;
    }

    public function getPullRequest(): PullRequest
    {
        return $this->pullRequest;
    }

    public function getEventName(): string
    {
        return Event::INSPECTION_STARTED;
    }
}
