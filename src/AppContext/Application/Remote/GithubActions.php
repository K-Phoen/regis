<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\Remote;

use League\Tactician\CommandBus;
use Regis\AppContext\Domain\Entity;
use Regis\GithubContext\Application\Command;
use Regis\GithubContext\Domain\Model\RepositoryIdentifier;

class GithubActions implements Actions
{
    private $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    public function createWebhook(Entity\Repository $repository, string $hookUrl)
    {
        $command = new Command\Repository\CreateWebhook(
            RepositoryIdentifier::fromFullName($repository->getIdentifier()),
            $hookUrl
        );

        $this->bus->handle($command);
    }
}
