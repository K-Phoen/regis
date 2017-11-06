<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\CommandHandler\Repository;

use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Application\Github\ClientFactory as GithubClientFactory;
use Regis\GithubContext\Domain\Entity;
use Regis\GithubContext\Domain\Repository\Repositories;

class CreateWebhook
{
    private $githubClientFactory;
    private $repositoriesRepo;

    public function __construct(GithubClientFactory $githubClientFactory, Repositories $repositoriesRepo)
    {
        $this->githubClientFactory = $githubClientFactory;
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function handle(Command\Repository\CreateWebhook $command)
    {
        /** @var Entity\Repository $repository */
        $repository = $this->repositoriesRepo->find($command->getRepository()->getIdentifier());
        $githubClient = $this->githubClientFactory->createForRepository($repository);

        $githubClient->createWebhook(
            $command->getRepository(),
            $command->getCallbackUrl(),
            $repository->getSharedSecret()
        );
    }
}
