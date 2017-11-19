<?php

declare(strict_types=1);

namespace Tests\Regis\AppContext\Application\Remote;

use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;
use Regis\AppContext\Application\Remote\Actions;
use Regis\AppContext\Application\Remote\BitbucketActions;
use Regis\AppContext\Domain\Entity;
use Regis\BitbucketContext\Application\Command;

class BitbucketActionsTest extends TestCase
{
    private $bus;
    private $actions;

    public function setUp()
    {
        $this->bus = $this->createMock(CommandBus::class);

        $this->actions = new BitbucketActions($this->bus);
    }

    public function testItImplementsTheActionsInterface()
    {
        $this->assertInstanceOf(Actions::class, $this->actions);
    }

    public function testItDelegatesTheWebhookCreationToTheCommandBus()
    {
        $repository = $this->createMock(Entity\Repository::class);
        $repository->method('getIdentifier')->willReturn('repo-id');

        $this->bus->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (Command\Repository\CreateWebhook $command) {
                $this->assertSame('repo-id', $command->getRepository()->value());
                $this->assertSame('hook-url', $command->getCallbackUrl());

                return true;
            }));

        $this->actions->createWebhook($repository, 'hook-url');
    }
}
