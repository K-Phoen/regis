<?php

declare(strict_types=1);

namespace Tests\Regis\AppContext\Application\Command\Team;

use PHPUnit\Framework\TestCase;
use RulerZ\Spec\Specification;
use Regis\AppContext\Application\Command;
use Regis\AppContext\Domain\Entity;

class AddRepositoryTest extends TestCase
{
    public function testCommandIsSecured()
    {
        $user = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $team = $this->getMockBuilder(Entity\Team::class)->disableOriginalConstructor()->getMock();

        $command = new Command\Team\AddRepository($team, 'new-repo-id');

        $this->assertInstanceOf(Command\SecureCommandBySpecification::class, $command);
        $this->assertInstanceOf(Specification::class, $command::executionAuthorizedFor($user));
        $this->assertSame($team, $command->getTargetToSecure());
    }
}
