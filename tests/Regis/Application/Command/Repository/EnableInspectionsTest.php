<?php

namespace Tests\Regis\Application\Command\Repository;

use PHPUnit\Framework\TestCase;
use RulerZ\Spec\Specification;

use Regis\Application\Command;
use Regis\Domain\Entity;

class EnableInspectionsTest extends TestCase
{
    public function testCommandIsSecured()
    {
        $user = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder(Entity\Repository::class)->disableOriginalConstructor()->getMock();

        $command = new Command\Repository\EnableInspections($repo);

        $this->assertInstanceOf(Command\SecureCommandBySpecification::class, $command);
        $this->assertInstanceOf(Specification::class, $command::executionAuthorizedFor($user));
        $this->assertSame($repo, $command->getTargetToSecure());
    }
}
