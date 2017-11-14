<?php

namespace Tests\Regis\AppContext\Application\Command\Team;

use PHPUnit\Framework\TestCase;
use RulerZ\Spec\Specification;

use Regis\AppContext\Application\Command;
use Regis\AppContext\Domain\Entity;

class RemoveRepositoryTest extends TestCase
{
    public function testCommandIsSecured()
    {
        $user = $this->createMock(Entity\User::class);
        $team = $this->createMock(Entity\Team::class);

        $command = new Command\Team\RemoveRepository($team, 'repo-id');

        $this->assertInstanceOf(Command\SecureCommandBySpecification::class, $command);
        $this->assertInstanceOf(Specification::class, $command::executionAuthorizedFor($user));
        $this->assertSame($team, $command->getTargetToSecure());
    }
}
