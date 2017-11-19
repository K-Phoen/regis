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

namespace Tests\Regis\AppContext\Domain\Model;

use PHPUnit\Framework\TestCase;
use Regis\AppContext\Domain\Model\Repository;

class RepositoryTest extends TestCase
{
    public function testItHoldsData()
    {
        $repo = new Repository('repo-identifier', 'repo-name', 'public-url', 'repo-type');

        $this->assertSame('repo-identifier', $repo->getIdentifier());
        $this->assertSame('repo-name', $repo->getName());
        $this->assertSame('public-url', $repo->getPublicUrl());
        $this->assertSame('repo-type', $repo->getType());
    }

    public function testItCanBeConvertedToAnArray()
    {
        $repo = new Repository('repo-identifier', 'repo-name', 'public-url', 'repo-type');

        $this->assertSame([
            'identifier' => 'repo-identifier',
            'name' => 'repo-name',
            'public_url' => 'public-url',
            'type' => 'repo-type',
        ], $repo->toArray());
    }
}
