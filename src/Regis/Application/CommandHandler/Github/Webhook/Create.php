<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Github\Webhook;

use Regis\Application\Command;
use Regis\Domain\Repository\Repositories;
use Regis\Infrastructure\Github\Client as GithubClient;

class Create
{
    private $githubClient;
    private $repositoriesRepo;

    public function __construct(GithubClient $githubClient, Repositories $repositoriesRepo)
    {
        $this->githubClient = $githubClient;
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function handle(Command\Github\Webhook\Create $command)
    {
        $repository = $this->repositoriesRepo->find($command->getOwner().'/'.$command->getRepo());

        $this->githubClient->createWebhook(
            $command->getOwner(),
            $command->getRepo(),
            $command->getCallbackUrl(),
            $repository->getSharedSecret()
        );
    }
}