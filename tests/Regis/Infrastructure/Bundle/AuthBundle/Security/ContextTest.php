<?php

namespace Tests\Regis\Infrastructure\Bundle\AuthBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Regis\Domain\Entity\User;
use Regis\Infrastructure\Bundle\AuthBundle\Security\Context;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    public function testItFetchesTheUserFromTheTokenStorage()
    {
        $tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $token = $this->getMockBuilder(TokenInterface::class)->getMock();
        $user = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();

        $context = new Context($tokenStorage);

        $tokenStorage->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($token));

        $token->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($user));

        $this->assertSame($user, $context->getUser());
    }
}
