<?php

namespace Tests\Regis\GithubContext\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Infrastructure\Repository\InMemoryRepositories;
use Regis\GithubContext\Domain\Entity;
use RulerZ\RulerZ;
use RulerZ\Spec\Specification;

class InMemoryRepositoriesTest extends TestCase
{
    private $owner;
    private $rulerz;

    public function setUp()
    {
        $this->rulerz = $this->createMock(RulerZ::class);
        $this->owner = $this->createMock(Entity\User::class);
    }

    public function testMatching()
    {
        $spec = $this->createMock(Specification::class);

        $repo = new InMemoryRepositories($this->rulerz, $originalData = [/* not relevant here */]);
        $results = new \ArrayIterator(['not relevant']);

        $this->rulerz->expects($this->once())
            ->method('filterSpec')
            ->with($originalData, $spec)
            ->willReturn($results);

        $this->assertSame($results, $repo->matching($spec));
    }

    /**
     * @expectedException \Regis\GithubContext\Domain\Repository\Exception\NotFound
     */
    public function testFindThrowAnExceptionIfTheEntityDoesNotExist()
    {
        $repo = new InMemoryRepositories($this->rulerz, [
            new Entity\Repository($this->owner, 'some identifier', 'shared secret'),
        ]);

        $repo->find('some identifier that does not exist');
    }

    public function testFindReturnsTheEntityIfItExists()
    {
        $repo = new InMemoryRepositories($this->rulerz, [
            new Entity\Repository($this->owner, 'some identifier', 'shared secret'),
        ]);

        $entity = $repo->find('some identifier');

        $this->assertInstanceOf(Entity\Repository::class, $entity);
        $this->assertSame('some identifier', $entity->getIdentifier());
        $this->assertSame('shared secret', $entity->getSharedSecret());
    }

    public function testARepositoryCanBeSaved()
    {
        $repo = new InMemoryRepositories($this->rulerz, []);

        $entity = new Entity\Repository($this->owner, 'some identifier', 'shared secret');
        $repo->save($entity);

        $this->assertSame($entity, $repo->find('some identifier'));
    }
}
