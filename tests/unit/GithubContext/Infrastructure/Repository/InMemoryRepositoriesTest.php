<?php

/*
 * Regis – Static analysis as a service
 * Copyright (C) 2016-2017 Kévin Gomez <contact@kevingomez.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
