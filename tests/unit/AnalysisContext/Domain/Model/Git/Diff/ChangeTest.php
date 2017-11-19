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

namespace Tests\Regis\AnalysisContext\Domain\Model\Git\Diff;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Domain\Model\Git;

class ChangeTest extends TestCase
{
    public function testItJustHoldsValues()
    {
        $change = new Git\Diff\Change(
            $oldStart = 1,
            $oldCount = 2,
            $newStart = 3,
            $newCount = 4,
            $lines = []
        );

        $this->assertSame($oldStart, $change->getRangeOldStart());
        $this->assertSame($oldCount, $change->getRangeOldCount());
        $this->assertSame($newStart, $change->getRangeNewStart());
        $this->assertSame($newCount, $change->getRangeNewCount());
        $this->assertSame($lines, $change->getLines());
    }
}
