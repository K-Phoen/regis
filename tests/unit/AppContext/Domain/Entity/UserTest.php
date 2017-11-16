<?php

namespace Tests\Regis\AppContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\AppContext\Domain\Entity\User;
use Regis\Kernel;
use Symfony\Component\Security\Core\User\UserInterface;
use Tests\Regis\Helper\ObjectManipulationHelper;

class UserTest extends TestCase
{
    use ObjectManipulationHelper;

    public function testItImplementsTheRightInterfaces()
    {
        $user = new User();
        $this->setPrivateValue($user, 'id', 'identifier');
        $this->setPrivateValue($user, 'roles', ['ROLE_USER']);

        $this->assertInstanceOf(Kernel\User::class, $user);
        $this->assertSame('identifier', $user->accountId());

        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertEmpty($user->getPassword());
        $this->assertNull($user->getSalt());
        $this->assertNull($user->eraseCredentials());
        $this->assertSame('identifier', $user->getUsername());
        $this->assertSame(['ROLE_USER'], $user->getRoles());
    }
}
