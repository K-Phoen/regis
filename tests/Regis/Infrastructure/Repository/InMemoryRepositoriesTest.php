<?php

namespace Tests\Regis\Infrastructure\Repository;

use Regis\Infrastructure\Repository\InMemoryRepositories;
use Regis\Domain\Entity;

class InMemoryRepositoriesTest extends \PHPUnit_Framework_TestCase
{
    private $owner;

    public function setUp()
    {
        $this->owner = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();
    }

    public function testFindForUser()
    {
        $otherOwner = $this->getMockBuilder(Entity\User::class)->disableOriginalConstructor()->getMock();

        $repo = new InMemoryRepositories([
            $expectedRepo = new Entity\Github\Repository($this->owner, 'k-phoen/test', 'some_awesome_secret'),
            new Entity\Github\Repository($otherOwner, 'k-phoen/test', 'some_awesome_secret'),
        ]);

        $this->assertInstanceOf(\Generator::class, $result = $repo->findForUser($this->owner));
        $this->assertCount(1, $data = iterator_to_array($result));
        $this->assertEquals([$expectedRepo], $data);
    }

    /**
     * @expectedException \Regis\Domain\Repository\Exception\NotFound
     */
    public function testFindThrowAnExceptionIfTheEntityDoesNotExist()
    {
        $repo = new InMemoryRepositories([
            new Entity\Github\Repository($this->owner, 'some identifier', 'shared secret'),
        ]);

        $repo->find('some identifier that does not exist');
    }

    public function testFindReturnsTheEntityIfItExists()
    {
        $repo = new InMemoryRepositories([
            new Entity\Github\Repository($this->owner, 'some identifier', 'shared secret'),
        ]);

        $entity = $repo->find('some identifier');

        $this->assertInstanceOf(Entity\Github\Repository::class, $entity);
        $this->assertSame('some identifier', $entity->getIdentifier());
        $this->assertSame('shared secret', $entity->getSharedSecret());
    }

    public function testARepositoryCanBeSaved()
    {
        $repo = new InMemoryRepositories([]);

        $entity = new Entity\Github\Repository($this->owner, 'some identifier', 'shared secret');
        $repo->save($entity);

        $this->assertSame($entity, $repo->find('some identifier'));
    }
}
