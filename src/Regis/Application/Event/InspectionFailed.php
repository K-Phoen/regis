<?php

declare(strict_types=1);

namespace Regis\Application\Event;

use Regis\Application\Event;
use Regis\Domain\Entity;
use Regis\Domain\Model\Github\PullRequest;

class InspectionFailed implements Event
{
    private $inspection;
    private $pullRequest;
    private $error;

    public function __construct(Entity\Inspection $inspection, PullRequest $pullRequest, \Exception $error)
    {
        $this->inspection = $inspection;
        $this->pullRequest = $pullRequest;
        $this->error = $error;
    }

    public function getInspection(): Entity\Inspection
    {
        return $this->inspection;
    }

    public function getPullRequest(): PullRequest
    {
        return $this->pullRequest;
    }

    public function getError(): \Exception
    {
        return $this->error;
    }

    public function getEventName(): string
    {
        return Event::INSPECTION_FAILED;
    }
}
