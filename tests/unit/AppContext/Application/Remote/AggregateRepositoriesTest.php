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

namespace Tests\Regis\AppContext\Application\Remote;

use PHPUnit\Framework\TestCase;
use Regis\AppContext\Domain\Entity\User;
use Regis\AppContext\Application\Remote\AggregatedRepositories;
use Regis\AppContext\Application\Remote\Repositories;

class AggregateRepositoriesTest extends TestCase
{
    public function testItAggregatesResultsFromSeveralSources()
    {
        $user = new User();
        $source = $this->createMock(Repositories::class);
        $otherSource = $this->createMock(Repositories::class);

        $source->method('forUser')->willReturn(new \ArrayIterator(['foo']));
        $otherSource->method('forUser')->willReturn(new \ArrayIterator(['bar']));

        $results = (new AggregatedRepositories([$source, $otherSource]))->forUser($user);

        $this->assertSame(['foo', 'bar'], iterator_to_array($results, false));
    }
}
