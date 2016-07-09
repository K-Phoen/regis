<?php

declare(strict_types=1);

namespace Regis\Application\Reporter;

use Regis\Application\Github\ClientFactory as GithubClientFactory;
use Regis\Application\Reporter;
use Regis\Domain\Entity;
use Regis\Domain\Model;

class Github implements Reporter
{
    private $clientFactory;

    public function __construct(GithubClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    public function report(Entity\Repository $repository, Entity\Inspection\Violation $violation, Model\Github\PullRequest $pullRequest)
    {
        /** @var Entity\Github\Repository $repository */
        $client = $this->clientFactory->createForRepository($repository);
        $client->sendComment($pullRequest, Model\Github\ReviewComment::fromViolation($violation));
    }
}
