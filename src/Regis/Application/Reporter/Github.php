<?php

declare(strict_types=1);

namespace Regis\Application\Reporter;

use Regis\Application\Github\Client as GithubClient;
use Regis\Application\Reporter;
use Regis\Domain\Entity;
use Regis\Domain\Model;

class Github implements Reporter
{
    private $client;

    public function __construct(GithubClient $client)
    {
        $this->client = $client;
    }

    public function report(Entity\Inspection\Violation $violation, Model\Github\PullRequest $pullRequest)
    {
        $this->client->sendComment($pullRequest, Model\Github\ReviewComment::fromViolation($violation));
    }
}