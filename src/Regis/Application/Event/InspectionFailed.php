<?php

declare(strict_types=1);

namespace Regis\Application\Event;

use Regis\Application\Event;
use Regis\Application\Model\Github\PullRequest;

class InspectionFailed implements Event
{
    private $pullRequest;
    private $error;

    public function __construct(PullRequest $pullRequest, \Exception $error)
    {
        $this->pullRequest = $pullRequest;
        $this->error = $error;
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