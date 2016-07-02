<?php

namespace Tests\Regis\Infrastructure\Repository;

use Regis\Infrastructure\Repository\InMemoryRepositories;
use Regis\Domain\Entity;

class InMemoryRepositoriesTest extends \PHPUnit_Framework_TestCase
{
    public function testFindAllAGeneratorToTheRepositories()
    {
        $repo = new InMemoryRepositories([
            'some identifier' => [
                'secret' => 'shared secret',
            ]
        ]);

        $this->assertInstanceOf(\Generator::class, $result = $repo->findAll());
        $this->assertCount(1, iterator_to_array($result));
    }

    /**
     * @expectedException \Regis\Domain\Repository\Exception\NotFound
     */
    public function testFindThrowAnExceptionIfTheEntityDoesNotExist()
    {
        $repo = new InMemoryRepositories([
            'some identifier' => [
                'secret' => 'shared secret',
            ]
        ]);

        $repo->find('some identifier that does not exist');
    }

    public function testFindReturnsTheEntityIfItExists()
    {
        $repo = new InMemoryRepositories([
            'some identifier' => [
                'secret' => 'shared secret',
            ]
        ]);

        $entity = $repo->find('some identifier');

        $this->assertInstanceOf(Entity\Github\Repository::class, $entity);
        $this->assertSame('some identifier', $entity->getIdentifier());
        $this->assertSame('shared secret', $entity->getSharedSecret());
    }

    public function testARepositoryCanBeSaved()
    {
        $repo = new InMemoryRepositories([]);

        $entity = new Entity\Github\Repository('some identifier', 'shared secret');
        $repo->save($entity);

        $this->assertSame($entity, $repo->find('some identifier'));
    }
}
