<?php

declare(strict_types=1);

namespace Tests\Regis\AppContext\Application\Command\Repository;

use PHPUnit\Framework\TestCase;
use RulerZ\Spec\Specification;
use Regis\AppContext\Application\Command;
use Regis\AppContext\Domain\Entity;

class DisableInspectionsTest extends TestCase
{
    public function testCommandIsSecured()
    {
        $user = $this->createMock(Entity\User::class);
        $repo = $this->createMock(Entity\Repository::class);

        $command = new Command\Repository\DisableInspections($repo);

        $this->assertInstanceOf(Command\SecureCommandBySpecification::class, $command);
        $this->assertInstanceOf(Specification::class, $command::executionAuthorizedFor($user));
        $this->assertSame($repo, $command->getTargetToSecure());
    }
}
