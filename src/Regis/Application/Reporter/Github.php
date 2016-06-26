<?php

declare(strict_types=1);

namespace Regis\Application\Reporter;

use Regis\Application\Entity;
use Regis\Application\Model;
use Regis\Application\Reporter;
use Regis\Github\Client;

class Github implements Reporter
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function report(Entity\Inspection\Violation $violation, Model\Github\PullRequest $pullRequest)
    {
        $this->client->sendComment($pullRequest, Model\Github\ReviewComment::fromViolation($violation));
    }
}