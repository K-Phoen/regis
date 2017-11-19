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

namespace Tests\Regis\BitbucketContext\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Regis\BitbucketContext\Domain\Model\Repository;
use Regis\BitbucketContext\Domain\Model\RepositoryIdentifier;

class RepositoryTest extends TestCase
{
    public function testItHoldsData()
    {
        $repoIdentifier = new RepositoryIdentifier('identifier-value');
        $repo = new Repository($repoIdentifier, 'repo-name', 'clone-url', 'public-url');

        $this->assertSame($repoIdentifier, $repo->getIdentifier());
        $this->assertSame('repo-name', $repo->getName());
        $this->assertSame('clone-url', $repo->getCloneUrl());
        $this->assertSame('public-url', $repo->getPublicUrl());
    }

    public function testItCanBeConvertedToAString()
    {
        $repoIdentifier = new RepositoryIdentifier('identifier-value');
        $repo = new Repository($repoIdentifier, 'repo-name', 'clone-url', 'public-url');

        $this->assertSame('identifier-value', (string) $repo);
    }

    public function testItCanBeConvertedToAnArray()
    {
        $repoIdentifier = new RepositoryIdentifier('identifier-value');
        $repo = new Repository($repoIdentifier, 'repo-name', 'clone-url', 'public-url');

        $this->assertSame([
            'identifier' => ['identifier' => 'identifier-value'],
            'name' => 'repo-name',
            'clone_url' => 'clone-url',
            'public_url' => 'public-url',
        ], $repo->toArray());
    }

    public function testItCanBeCreatedFromAnArray()
    {
        $repo = Repository::fromArray([
            'identifier' => ['identifier' => 'identifier-value'],
            'name' => 'repo-name',
            'clone_url' => 'clone-url',
            'public_url' => 'public-url',
        ]);

        $this->assertSame('identifier-value', $repo->getIdentifier()->value());
        $this->assertSame('repo-name', $repo->getName());
        $this->assertSame('clone-url', $repo->getCloneUrl());
        $this->assertSame('public-url', $repo->getPublicUrl());
    }
}
