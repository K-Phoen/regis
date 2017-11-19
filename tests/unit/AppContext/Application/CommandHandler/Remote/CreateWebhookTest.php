<?php

declare(strict_types=1);

namespace Tests\Regis\AppContext\Application\CommandHandler\Remote;

use PHPUnit\Framework\TestCase;
use Regis\AppContext\Application\Command;
use Regis\AppContext\Application\CommandHandler;
use Regis\AppContext\Application\Remote\ActionsRouter;
use Regis\AppContext\Domain\Entity;

class CreateWebhookTest extends TestCase
{
    private $actionsRouter;
    /** @var CommandHandler\Remote\CreateWebhook */
    private $handler;

    public function setUp()
    {
        $this->actionsRouter = $this->createMock(ActionsRouter::class);

        $this->handler = new CommandHandler\Remote\CreateWebhook($this->actionsRouter);
    }

    public function testItDelegatesTheWorkToTheActionRouter()
    {
        $repository = $this->createMock(Entity\Repository::class);
        $command = new Command\Remote\CreateWebhook($repository, 'hook-url');

        $this->actionsRouter->expects($this->once())
            ->method('createWebhook')
            ->with($repository, 'hook-url');

        $this->handler->handle($command);
    }
}
