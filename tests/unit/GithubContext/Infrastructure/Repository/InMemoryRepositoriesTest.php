<?php

declare(strict_types=1);

namespace Tests\Regis\GithubContext\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use Regis\GithubContext\Infrastructure\Repository\InMemoryRepositories;
use Regis\GithubContext\Domain\Entity;
use Tests\Regis\Helper\ObjectManipulationHelper;

class InMemoryRepositoriesTest extends TestCase
{
    use ObjectManipulationHelper;

    private $owner;

    public function setUp()
    {
        $this->owner = $this->createMock(Entity\UserAccount::class);
    }

    /**
     * @expectedException \Regis\GithubContext\Domain\Repository\Exception\NotFound
     */
    public function testFindThrowAnExceptionIfTheEntityDoesNotExist()
    {
        $repo = new InMemoryRepositories([$this->getRepository()]);

        $repo->find('some identifier that does not exist');
    }

    public function testFindReturnsTheEntityIfItExists()
    {
        $repo = new InMemoryRepositories([$this->getRepository()]);

        $entity = $repo->find('some identifier');

        $this->assertInstanceOf(Entity\Repository::class, $entity);
        $this->assertSame('some identifier', $entity->getIdentifier());
        $this->assertSame('shared secret', $entity->getSharedSecret());
    }

    public function testARepositoryCanBeSaved()
    {
        $repo = new InMemoryRepositories([]);

        $entity = $this->getRepository();
        $repo->save($entity);

        $this->assertSame($entity, $repo->find('some identifier'));
    }

    private function getRepository(): Entity\Repository
    {
        $repository = new Entity\Repository();
        $this->setPrivateValue($repository, 'owner', $this->owner);
        $this->setPrivateValue($repository, 'identifier', 'some identifier');
        $this->setPrivateValue($repository, 'sharedSecret', 'shared secret');

        return $repository;
    }
}
