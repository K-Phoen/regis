<?php

declare(strict_types=1);

namespace Tests\Regis\BitbucketContext\Application\CommandHandler\Inspection;

use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Application\Command;
use Regis\BitbucketContext\Application\CommandHandler;
use Regis\BitbucketContext\Domain\Model;
use Tests\Regis\Helper\ObjectManipulationHelper;
use Regis\BitbucketContext\Application\Inspection\ViolationsCache;

class ClearViolationsCacheTest extends TestCase
{
    use ObjectManipulationHelper;

    private $violationsCache;

    /** @var CommandHandler\Inspection\ClearViolationsCache */
    private $handler;

    public function setUp()
    {
        $this->violationsCache = $this->createMock(ViolationsCache::class);

        $this->handler = new CommandHandler\Inspection\ClearViolationsCache($this->violationsCache);
    }

    public function testItClearsTheViolationCache()
    {
        $pullRequest = $this->createMock(Model\PullRequest::class);
        $command = new Command\Inspection\ClearViolationsCache($pullRequest);

        $this->violationsCache->expects($this->once())
            ->method('clear')
            ->with($pullRequest);

        $this->handler->handle($command);
    }
}
