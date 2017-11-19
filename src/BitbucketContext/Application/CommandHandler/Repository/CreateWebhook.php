<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\CommandHandler\Repository;

use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Application\Bitbucket\ClientFactory as BitbucketClientFactory;
use Regis\BitbucketContext\Domain\Entity;
use Regis\BitbucketContext\Domain\Repository\Repositories;

class CreateWebhook
{
    private $bitbucketClientFactory;
    private $repositoriesRepo;

    public function __construct(BitbucketClientFactory $githubClientFactory, Repositories $repositoriesRepo)
    {
        $this->bitbucketClientFactory = $githubClientFactory;
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function handle(Command\Repository\CreateWebhook $command)
    {
        /** @var Entity\Repository $repository */
        $repository = $this->repositoriesRepo->find($command->getRepository()->value());
        $githubClient = $this->bitbucketClientFactory->createForRepository($repository);

        $githubClient->createWebhook($command->getRepository(), $command->getCallbackUrl());
    }
}