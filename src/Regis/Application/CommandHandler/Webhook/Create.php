<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Webhook;

use Regis\Github\Client as GithubClient;
use Regis\Application\Command;
use Regis\Application\Repository\Repositories;

class Create
{
    private $githubClient;
    private $repositoriesRepo;

    public function __construct(GithubClient $githubClient, Repositories $repositoriesRepo)
    {
        $this->githubClient = $githubClient;
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function handle(Command\Webhook\Create $command)
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