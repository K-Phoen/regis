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

namespace Tests\Regis\AppContext\Infrastructure\Twig;

use PHPUnit\Framework\TestCase;
use Regis\AppContext\Infrastructure\Twig\DurationExtension;

class DurationExtensionTest extends TestCase
{
    /** @var DurationExtension */
    private $extension;

    public function setUp()
    {
        $this->extension = new DurationExtension();
    }

    public function testItRegistersAFilter()
    {
        $this->assertCount(1, $this->extension->getFilters());
    }

    /**
     * @dataProvider secondsProvider
     */
    public function testTheDurationFilterSeemsToWork(int $seconds, string $expectedResult)
    {
        $this->assertSame($expectedResult, $this->extension->durationFilter($seconds));
    }

    public function secondsProvider()
    {
        return [
            [1, '1 sec'],
            [5, '5 sec'],
            [59, '59 sec'],

            [60, '1 min'],
            [65, '1 min 5 sec'],
            [119, '1 min 59 sec'],
            [120, '2 min'],

            [3600, '1 hour(s)'],
            [3601, '1 hour(s) 1 sec'],
        ];
    }
}
