<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\Remote;

use League\Tactician\CommandBus;
use Regis\AppContext\Domain\Entity;
use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Domain\Model\RepositoryIdentifier;

class BitbucketActions implements Actions
{
    private $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    public function createWebhook(Entity\Repository $repository, string $hookUrl)
    {
        $command = new Command\Repository\CreateWebhook(
            new RepositoryIdentifier($repository->getIdentifier()),
            $hookUrl
        );

        $this->bus->handle($command);
    }
}
