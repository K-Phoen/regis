<?php

namespace Tests\Regis\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\Domain\Entity\User;

class UserTest extends TestCase
{
    public function testAnAdminCanBeCreated()
    {
        $admin = User::createAdmin('admin', 'encoded password', 'email');

        $this->assertNotEmpty($admin->getId());
        $this->assertEquals('admin', $admin->getUsername());
        $this->assertEquals('email', $admin->getEmail());
        $this->assertEquals('encoded password', $admin->getPassword());
        $this->assertEquals(['ROLE_ADMIN'], $admin->getRoles());
    }

    public function testAUserCanBeCreatedWithNoEmail()
    {
        $user = User::createUser('user', 42, 'access token');

        $this->assertNotEmpty($user->getId());
        $this->assertSame('user', $user->getUsername());
        $this->assertSame(42, $user->getGithubId());
        $this->assertSame('access token', $user->getGithubAccessToken());
        $this->assertNull($user->getEmail());
        $this->assertNull($user->getPassword());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The new password can not be empty
     */
    public function testAnEmptyPasswordCanNotBeUsed()
    {
        User::createAdmin('admin', '', 'email');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The new email can not be empty
     */
    public function testAnEmptyEmailCanNotBeUsed()
    {
        User::createAdmin('admin', 'password', '');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The new password can not be empty
     */
    public function testThePasswordCanNotBeReplacedByAnEmptyOne()
    {
        $admin = User::createAdmin('admin', 'encoded password', 'email');
        $admin->changePassword('');
    }
}
