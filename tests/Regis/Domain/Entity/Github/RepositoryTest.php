<?php

namespace Tests\Regis\Domain\Entity\Github;

use Regis\Domain\Entity\Github\Repository;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testItHasAType()
    {
        $repository = new Repository('K-Phoen/test');

        $this->assertSame(Repository::TYPE_GITHUB, $repository->getType());
    }

    public function testItComputesTheOwner()
    {
        $repository = new Repository('K-Phoen/test');

        $this->assertSame('K-Phoen', $repository->getOwner());
    }

    public function testItComputesTheName()
    {
        $repository = new Repository('K-Phoen/test');

        $this->assertSame('test', $repository->getName());
    }

    public function testItCanChangeTheSecret()
    {
        $repository = new Repository('K-Phoen/test', 'initial secret');

        $this->assertSame('initial secret', $repository->getSharedSecret());

        $repository->newSharedSecret('new secret');

        $this->assertSame('new secret', $repository->getSharedSecret());
    }
}
