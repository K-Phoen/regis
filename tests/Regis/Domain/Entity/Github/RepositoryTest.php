<?php

namespace Tests\Regis\Domain\Entity\Github;

use PHPUnit\Framework\TestCase;
use Regis\Domain\Entity;
use Regis\Domain\Entity\Github\Repository;

class RepositoryTest extends TestCase
{
    private $owner;

    public function setUp()
    {
        $this->owner = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
    }

    public function testItHasAType()
    {
        $repository = new Repository($this->owner, 'K-Phoen/test');

        $this->assertSame(Repository::TYPE_GITHUB, $repository->getType());
    }

    public function testItComputesTheOwner()
    {
        $repository = new Repository($this->owner, 'K-Phoen/test');

        $this->assertSame('K-Phoen', $repository->getOwnerUsername());
    }

    public function testItComputesTheName()
    {
        $repository = new Repository($this->owner, 'K-Phoen/test');

        $this->assertSame('test', $repository->getName());
    }

    public function testItCanChangeTheSecret()
    {
        $repository = new Repository($this->owner, 'K-Phoen/test', 'initial secret');

        $this->assertSame('initial secret', $repository->getSharedSecret());

        $repository->newSharedSecret('new secret');

        $this->assertSame('new secret', $repository->getSharedSecret());
    }
}
