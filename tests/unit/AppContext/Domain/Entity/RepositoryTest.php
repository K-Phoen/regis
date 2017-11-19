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
