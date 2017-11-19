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
use Regis\AnalysisContext\Domain\Model\Git\Diff\Change;
use Regis\AnalysisContext\Domain\Model\Git\Diff\Line;

class LineTest extends TestCase
{
    public function testConstruction()
    {
        $line = new Line(Change::LINE_ADD, 2, 42, 'line content');

        $this->assertTrue($line->isAddition());
        $this->assertFalse($line->isDeletion());
        $this->assertFalse($line->isContext());
        $this->assertSame(2, $line->getPosition());
        $this->assertSame(42, $line->getNumber());
        $this->assertSame(Change::LINE_ADD, $line->getChangeType());
        $this->assertSame('line content', $line->getContent());
    }

    public function testDeletedLine()
    {
        $line = new Line(Change::LINE_REMOVE, 2, 42, 'line content');

        $this->assertTrue($line->isDeletion());
        $this->assertFalse($line->isContext());
        $this->assertFalse($line->isAddition());
        $this->assertSame(Change::LINE_REMOVE, $line->getChangeType());
    }

    public function testContextLine()
    {
        $line = new Line(Change::LINE_CONTEXT, 2, 42, 'line content');

        $this->assertTrue($line->isContext());
        $this->assertFalse($line->isAddition());
        $this->assertFalse($line->isDeletion());
        $this->assertSame(Change::LINE_CONTEXT, $line->getChangeType());
    }
}
