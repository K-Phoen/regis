<?php

declare(strict_types=1);

namespace Regis\Application\Event;

use Regis\Application\Entity;
use Regis\Application\Event;
use Regis\Application\Model\Github\PullRequest;

class InspectionFailed implements Event
{
    private $inspection;
    private $pullRequest;
    private $error;
    private $failedAt;

    public function __construct(Entity\Inspection $inspection, PullRequest $pullRequest, \Exception $error)
    {
        $this->inspection = $inspection;
        $this->pullRequest = $pullRequest;
        $this->error = $error;
        $this->failedAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
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

    public function getFailedAt(): \DateTimeInterface
    {
        return $this->failedAt;
    }

    public function getEventName(): string
    {
        return Event::INSPECTION_FAILED;
    }
}