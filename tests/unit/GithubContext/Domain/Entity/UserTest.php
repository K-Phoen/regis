<?php

namespace Tests\Regis\GithubContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Domain\Entity\User;

class UserTest extends TestCase
{
    public function testAnAdminCanBeCreated()
    {
        $admin = User::createAdmin('admin', 'encoded password');

        $this->assertNotEmpty($admin->getId());
        $this->assertEquals('admin', $admin->getUsername());
        $this->assertEquals('encoded password', $admin->getPassword());
        $this->assertEquals(['ROLE_ADMIN'], $admin->getRoles());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The new password can not be empty
     */
    public function testAnEmptyPasswordCanNotBeUsed()
    {
        User::createAdmin('admin', '');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The new password can not be empty
     */
    public function testThePasswordCanNotBeReplacedByAnEmptyOne()
    {
        $admin = User::createAdmin('admin', 'encoded password');
        $admin->changePassword('');
    }
}
