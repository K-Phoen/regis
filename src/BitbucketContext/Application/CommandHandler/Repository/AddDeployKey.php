<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Application\CommandHandler\Repository;

use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Application\Bitbucket\ClientFactory as BitbucketClientFactory;
use Regis\BitbucketContext\Domain\Entity;
use Regis\BitbucketContext\Domain\Repository\Repositories;

class AddDeployKey
{
    const KEY_TITLE = 'Regis deploy key';

    private $githubClientFactory;
    private $repositoriesRepo;

    public function __construct(BitbucketClientFactory $githubClientFactory, Repositories $repositoriesRepo)
    {
        $this->githubClientFactory = $githubClientFactory;
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function handle(Command\Repository\AddDeployKey $command)
    {
        /** @var Entity\Repository $repository */
        $repository = $this->repositoriesRepo->find($command->getRepository()->value());
        $bitbucketClient = $this->githubClientFactory->createForRepository($repository);

        $bitbucketClient->addDeployKey($repository->toIdentifier(), self::KEY_TITLE, $command->getKeyContent());
    }
}
