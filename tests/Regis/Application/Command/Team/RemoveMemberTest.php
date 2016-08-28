<?php

namespace Tests\Regis\Application\Command\Team;

use RulerZ\Spec\Specification;

use Regis\Application\Command;
use Regis\Domain\Entity;

class RemoveMemberTest extends \PHPUnit_Framework_TestCase
{
    public function testCommandIsSecured()
    {
        $user = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $team = $this->getMockBuilder(Entity\Team::class)->disableOriginalConstructor()->getMock();

        $command = new Command\Team\RemoveMember($team, 'member-id');

        $this->assertInstanceOf(Command\SecureCommandBySpecification::class, $command);
        $this->assertInstanceOf(Specification::class, $command::executionAuthorizedFor($user));
        $this->assertSame($team, $command->getTargetToSecure());
    }
}
