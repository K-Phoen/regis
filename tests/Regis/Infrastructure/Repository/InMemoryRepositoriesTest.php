<?php

namespace Tests\Regis\Infrastructure\Repository;

use Regis\Infrastructure\Repository\InMemoryRepositories;
use Regis\Domain\Entity;
use RulerZ\RulerZ;
use RulerZ\Spec\Specification;

class InMemoryRepositoriesTest extends \PHPUnit_Framework_TestCase
{
    private $owner;
    private $rulerz;

    public function setUp()
    {
        $this->rulerz = $this->getMockBuilder(RulerZ::class)->disableOriginalConstructor()->getMock();
        $this->owner = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
    }

    public function testMatching()
    {
        $spec = $this->getMockBuilder(Specification::class)->getMock();

        $repo = new InMemoryRepositories($this->rulerz, $originalData = [/* not relevant here */]);
        $results = new \ArrayIterator(['not relevant']);

        $this->rulerz->expects($this->once())
            ->method('filterSpec')
            ->with($originalData, $spec)
            ->will($this->returnValue($results));

        $this->assertSame($results, $repo->matching($spec));
    }

    /**
     * @expectedException \Regis\Domain\Repository\Exception\NotFound
     */
    public function testFindThrowAnExceptionIfTheEntityDoesNotExist()
    {
        $repo = new InMemoryRepositories($this->rulerz, [
            new Entity\Github\Repository($this->owner, 'some identifier', 'shared secret'),
        ]);

        $repo->find('some identifier that does not exist');
    }

    public function testFindReturnsTheEntityIfItExists()
    {
        $repo = new InMemoryRepositories($this->rulerz, [
            new Entity\Github\Repository($this->owner, 'some identifier', 'shared secret'),
        ]);

        $entity = $repo->find('some identifier');

        $this->assertInstanceOf(Entity\Github\Repository::class, $entity);
        $this->assertSame('some identifier', $entity->getIdentifier());
        $this->assertSame('shared secret', $entity->getSharedSecret());
    }

    public function testARepositoryCanBeSaved()
    {
        $repo = new InMemoryRepositories($this->rulerz, []);

        $entity = new Entity\Github\Repository($this->owner, 'some identifier', 'shared secret');
        $repo->save($entity);

        $this->assertSame($entity, $repo->find('some identifier'));
    }
}
