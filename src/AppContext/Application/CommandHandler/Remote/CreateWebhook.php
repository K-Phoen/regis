<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\CommandHandler\Remote;

use Regis\AppContext\Application\Remote\ActionsRouter;
use Regis\AppContext\Application\Command;

class CreateWebhook
{
    private $actionsRouter;

    public function __construct(ActionsRouter $actionsRouter)
    {
        $this->actionsRouter = $actionsRouter;
    }

    public function handle(Command\Remote\CreateWebhook $command)
    {
        $this->actionsRouter->createWebhook($command->getRepository(), $command->getUrl());
    }
}
