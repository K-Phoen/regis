<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\CommandHandler\Repository;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\Github\Client as GithubClient;
use Regis\GithubContext\Application\Github\ClientFactory as GithubClientFactory;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository\Repositories;

class AddDeployKey
{
    const KEY_TITLE = 'Regis - Private repositories';

    private $githubClientFactory;
    private $repositoriesRepo;

    public function __construct(GithubClientFactory $githubClientFactory, Repositories $repositoriesRepo)
    {
        $this->githubClientFactory = $githubClientFactory;
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function handle(Command\Repository\AddDeployKey $command)
    {
        /** @var Entity\Repository $repository */
        $repository = $this->repositoriesRepo->find($command->getRepositoryIdentifier());
        $githubClient = $this->githubClientFactory->createForRepository($repository);

        $githubClient->addDeployKey(
            $repository->toIdentifier(),
            self::KEY_TITLE,
            $command->getKeyContent(),
            GithubClient::READONLY_KEY
        );
    }
}
