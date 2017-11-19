<?php

declare(strict_types=1);

namespace Tests\Regis\AppContext\Application\Remote;

use PHPUnit\Framework\TestCase;
use Regis\AppContext\Application\Remote\Actions;
use Regis\AppContext\Application\Remote\ActionsRouter;
use Regis\AppContext\Domain\Entity;

class ActionsRouterTest extends TestCase
{
    public function testItImplementsTheActionsInterface()
    {
        $router = new ActionsRouter([]);

        $this->assertInstanceOf(Actions::class, $router);
    }

    public function testItDelegatesTheWebhookCreationToTheRightImplementation()
    {
        $githubActions = $this->createMock(Actions::class);
        $bitbucketActions = $this->createMock(Actions::class);
        $repository = $this->createMock(Entity\Repository::class);

        $repository->method('getType')->willReturn(Entity\Repository::TYPE_BITBUCKET);

        $router = new ActionsRouter([
            'github' => $githubActions,
            'bitbucket' => $bitbucketActions,
        ]);

        $githubActions->expects($this->never())->method('createWebhook');
        $bitbucketActions->expects($this->once())->method('createWebhook')->with($repository, 'hook-url');

        $router->createWebhook($repository, 'hook-url');
    }

    public function testItThrowsAnErrorIfNoImplementationIsFound()
    {
        $repository = $this->createMock(Entity\Repository::class);
        $repository->method('getType')->willReturn(Entity\Repository::TYPE_BITBUCKET);
        $router = new ActionsRouter([]);

        $this->expectException(\LogicException::class);

        $router->createWebhook($repository, 'hook-url');
    }
}
