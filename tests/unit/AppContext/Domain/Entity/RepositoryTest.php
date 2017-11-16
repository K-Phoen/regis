<?php

namespace Tests\Regis\AppContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\AppContext\Domain\Entity\Repository;
use Regis\Kernel;

class RepositoryTest extends TestCase
{
    public function testAnIdentifierIsGenerated()
    {
        $owner = $this->createMock(Kernel\User::class);
        $repository = new Repository($owner, Repository::TYPE_GITHUB, 'repo-identifier', 'name');

        $this->assertNotEmpty($repository->getId());
    }

    public function testTheBasicInformationCanBeAccessed()
    {
        $owner = $this->createMock(Kernel\User::class);
        $repository = new Repository($owner, Repository::TYPE_GITHUB, 'repo-identifier', 'name');

        $this->assertSame(Repository::TYPE_GITHUB, $repository->getType());
        $this->assertSame('repo-identifier', $repository->getIdentifier());
        $this->assertSame('name', $repository->getName());
        $this->assertSame($owner, $repository->getOwner());
        $this->assertEmpty($repository->getInspections());
        $this->assertEmpty($repository->getTeams());
    }
    
    public function testTheSharedSecretCanBeDefinedAndUpdated()
    {
        $owner = $this->createMock(Kernel\User::class);
        $repository = new Repository($owner, Repository::TYPE_GITHUB, 'repo-identifier', 'name', 'shared secret');

        $this->assertSame('shared secret', $repository->getSharedSecret());
        
        $repository->newSharedSecret('new secret');
        $this->assertSame('new secret', $repository->getSharedSecret());
    }
}
