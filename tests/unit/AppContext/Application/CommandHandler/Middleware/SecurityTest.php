<?php

declare(strict_types=1);

namespace Tests\Regis\AppContext\Application\CommandHandler\Middleware;

use PHPUnit\Framework\TestCase;
use RulerZ\RulerZ;
use RulerZ\Spec\Specification;
use Regis\AppContext\Domain\Entity\User;
use Regis\Kernel\Security\Context;
use Regis\AppContext\Application\Command;
use Regis\AppContext\Application\CommandHandler\Middleware;

class SecurityTest extends TestCase
{
    private $rulerz;
    private $securityContext;
    private $user;
    private $middleware;

    public function setUp()
    {
        $this->rulerz = $this->createMock(RulerZ::class);
        $this->securityContext = $this->createMock(Context::class);
        $this->user = $this->createMock(User::class);

        $this->securityContext->method('getUser')->willReturn($this->user);

        $this->middleware = new Middleware\Security($this->rulerz, $this->securityContext);
    }

    public function testNotSecureCommandsArePassedThrough()
    {
        $command = 'not an instance of something secure';
        $nextCalled = false;

        $next = function ($command) use (&$nextCalled) {
            $nextCalled = true;

            return 'return value';
        };

        $return = $this->middleware->execute($command, $next);

        $this->assertTrue($nextCalled);
        $this->assertSame('return value', $return);
    }

    public function testASecureCommandWhenTheAuthorizationIsGiven()
    {
        $command = $this->createMock(Command\SecureCommand::class);
        $nextCalled = false;

        $next = function ($command) use (&$nextCalled) {
            $nextCalled = true;
        };

        $command->expects($this->once())
            ->method('executionAuthorizedFor')
            ->with($this->user)
            ->willReturn(true);

        $this->middleware->execute($command, $next);

        $this->assertTrue($nextCalled);
    }

    /**
     * @expectedException \Regis\Kernel\Security\Exception\AccessDenied
     */
    public function testASecureCommandWhenTheAuthorizationIsNotGiven()
    {
        $command = $this->createMock(Command\SecureCommand::class);
        $next = function ($command) {
        };

        $command
            ->method('executionAuthorizedFor')
            ->with($this->user)
            ->willReturn(false);

        $this->middleware->execute($command, $next);
    }

    public function testASecureCommandBySpecificationWhenTheAuthorizationIsGiven()
    {
        $target = new \stdClass();
        $spec = $this->createMock(Specification::class);
        $command = $this->createCommandSecuredBySpec($spec, $target);
        $nextCalled = false;

        $next = function ($command) use (&$nextCalled) {
            $nextCalled = true;
        };

        $this->rulerz->expects($this->once())
            ->method('satisfiesSpec')
            ->with($target, $spec)
            ->willReturn(true);

        $this->middleware->execute($command, $next);

        $this->assertTrue($nextCalled);
    }

    /**
     * @expectedException \Regis\Kernel\Security\Exception\AccessDenied
     */
    public function testASecureCommandBySpecificationWhenTheAuthorizationIsNotGiven()
    {
        $target = new \stdClass();
        $spec = $this->createMock(Specification::class);
        $command = $this->createCommandSecuredBySpec($spec, $target);

        $next = function ($command) {
        };

        $this->rulerz
            ->method('satisfiesSpec')
            ->with($target, $spec)
            ->willReturn(false);

        $this->middleware->execute($command, $next);
    }

    private function createCommandSecuredBySpec(Specification $spec, $target)
    {
        return new class($spec, $target) implements Command\SecureCommandBySpecification {
            public static $spec;
            public static $target;

            public function __construct(Specification $spec, $target = null)
            {
                self::$spec = $spec;
                self::$target = $target;
            }

            public static function executionAuthorizedFor(User $user): Specification
            {
                return self::$spec;
            }

            public function getTargetToSecure()
            {
                return self::$target;
            }
        };
    }
}
