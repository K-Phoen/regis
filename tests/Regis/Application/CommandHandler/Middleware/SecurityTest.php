<?php

namespace Tests\Regis\Application\CommandHandler\Middleware;

use Regis\Domain\Entity\User;
use RulerZ\RulerZ;

use Regis\Application\Command;
use Regis\Application\CommandHandler\Middleware;
use Regis\Application\Security\Context;
use RulerZ\Spec\Specification;

class SecurityTest extends \PHPUnit_Framework_TestCase
{
    private $rulerz;
    private $securityContext;
    private $user;
    private $middleware;

    public function setUp()
    {
        $this->rulerz = $this->getMockBuilder(RulerZ::class)->disableOriginalConstructor()->getMock();
        $this->securityContext = $this->getMockBuilder(Context::class)->getMock();
        $this->user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();

        $this->securityContext->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($this->user));

        $this->middleware = new Middleware\Security($this->rulerz, $this->securityContext);
    }

    public function testNotSecureCommandsArePassedThrough()
    {
        $command = 'not an instance of something secure';
        $nextCalled = false;

        $next = function($command) use (&$nextCalled) {
            $nextCalled = true;
        };

        $this->middleware->execute($command, $next);

        $this->assertTrue($nextCalled);
    }

    public function testASecureCommandWhenTheAuthorizationIsGiven()
    {
        $command = $this->getMockBuilder(Command\SecureCommand::class)->getMock();
        $nextCalled = false;

        $next = function($command) use (&$nextCalled) {
            $nextCalled = true;
        };

        $command->expects($this->once())
            ->method('executionAuthorizedFor')
            ->with($this->user)
            ->will($this->returnValue(true));

        $this->middleware->execute($command, $next);

        $this->assertTrue($nextCalled);
    }

    /**
     * @expectedException \Regis\Application\Security\Exception\AccessDenied
     */
    public function testASecureCommandWhenTheAuthorizationIsNotGiven()
    {
        $command = $this->getMockBuilder(Command\SecureCommand::class)->getMock();
        $next = function($command) {};

        $command->expects($this->once())
            ->method('executionAuthorizedFor')
            ->with($this->user)
            ->will($this->returnValue(false));

        $this->middleware->execute($command, $next);
    }

    public function testASecureCommandBySpecificationWhenTheAuthorizationIsGiven()
    {
        $target = new \stdClass();
        $command = $this->getMockBuilder(Command\SecureCommandBySpecification::class)->getMock();
        $spec = $this->getMockBuilder(Specification::class)->getMock();
        $nextCalled = false;

        $next = function($command) use (&$nextCalled) {
            $nextCalled = true;
        };

        $command->expects($this->once())
            ->method('executionAuthorizedFor')
            ->with($this->user)
            ->will($this->returnValue($spec));
        $command->expects($this->once())
            ->method('getTargetToSecure')
            ->will($this->returnValue($target));

        $this->rulerz->expects($this->once())
            ->method('satisfiesSpec')
            ->with($target, $spec)
            ->will($this->returnValue(true));

        $this->middleware->execute($command, $next);

        $this->assertTrue($nextCalled);
    }

    /**
     * @expectedException \Regis\Application\Security\Exception\AccessDenied
     */
    public function testASecureCommandBySpecificationWhenTheAuthorizationIsNotGiven()
    {
        $target = new \stdClass();
        $command = $this->getMockBuilder(Command\SecureCommandBySpecification::class)->getMock();
        $spec = $this->getMockBuilder(Specification::class)->getMock();

        $next = function($command) use (&$nextCalled) {};

        $command->expects($this->once())
            ->method('executionAuthorizedFor')
            ->with($this->user)
            ->will($this->returnValue($spec));
        $command->expects($this->once())
            ->method('getTargetToSecure')
            ->will($this->returnValue($target));

        $this->rulerz->expects($this->once())
            ->method('satisfiesSpec')
            ->with($target, $spec)
            ->will($this->returnValue(false));

        $this->middleware->execute($command, $next);
    }
}
