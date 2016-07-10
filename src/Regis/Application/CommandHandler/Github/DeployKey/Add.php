<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Github\DeployKey;

use Regis\Application\Command;
use Regis\Application\Github\Client as GithubClient;
use Regis\Application\Github\ClientFactory as GithubClientFactory;
use Regis\Domain\Entity;
use Regis\Domain\Repository\Repositories;

class Add
{
    const KEY_TITLE = 'Regis - Private repositories';

    private $githubClientFactory;
    private $repositoriesRepo;

    public function __construct(GithubClientFactory $githubClientFactory, Repositories $repositoriesRepo)
    {
        $this->githubClientFactory = $githubClientFactory;
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function handle(Command\Github\DeployKey\Add $command)
    {
        /** @var Entity\Github\Repository $repository */
        $repository = $this->repositoriesRepo->find($command->getRepositoryIdentifier());
        $githubClient = $this->githubClientFactory->createForRepository($repository);

        $githubClient->addDeployKey(
            $repository->getOwnerUsername(),
            $repository->getName(),
            self::KEY_TITLE,
            $command->getKeyContent(),
            GithubClient::READONLY_KEY
        );
    }
}
