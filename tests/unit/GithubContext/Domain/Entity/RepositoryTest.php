<?php

namespace Tests\Regis\GithubContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Domain\Entity\Repository;
use Regis\GithubContext\Domain\Entity\User;

class RepositoryTest extends TestCase
{
    private $owner;

    public function setUp()
    {
        $this->owner = $this->createMock(User::class);
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
