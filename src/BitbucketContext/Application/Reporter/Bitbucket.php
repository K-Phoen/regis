<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\Reporter;

use Regis\BitbucketContext\Application\Bitbucket\ClientFactory as BitbucketClientFactory;
use Regis\BitbucketContext\Application\Reporter;
use Regis\BitbucketContext\Domain\Entity;
use Regis\BitbucketContext\Domain\Model;

class Bitbucket implements Reporter
{
    private $clientFactory;

    public function __construct(BitbucketClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    public function report(Entity\Repository $repository, Model\ReviewComment $comment, Model\PullRequest $pullRequest)
    {
        /** @var Entity\Repository $repository */
        $client = $this->clientFactory->createForRepository($repository);
        $client->sendComment($pullRequest, $comment);
    }
}
