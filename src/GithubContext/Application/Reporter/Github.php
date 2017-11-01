<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Reporter;

use Regis\GithubContext\Application\Github\ClientFactory as GithubClientFactory;
use Regis\GithubContext\Application\Reporter;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Model;

class Github implements Reporter
{
    private $clientFactory;

    public function __construct(GithubClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    public function report(Entity\Repository $repository, Entity\Inspection\Violation $violation, Model\PullRequest $pullRequest)
    {
        /** @var Entity\Repository $repository */
        $client = $this->clientFactory->createForRepository($repository);
        $client->sendComment($pullRequest, Model\ReviewComment::fromViolation($violation));
    }
}
