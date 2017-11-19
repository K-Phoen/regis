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

namespace Tests\Regis\AnalysisContext\Domain\Model\Git;

use PHPUnit\Framework\TestCase;
use Regis\AnalysisContext\Domain\Model\Git;

class RevisionsTest extends TestCase
{
    public function testItCanBeConstructedFromAnArray()
    {
        $revisions = Git\Revisions::fromArray([
            'base' => 'base sha',
            'head' => 'head sha',
        ]);

        $this->assertSame('base sha', $revisions->getBase());
        $this->assertSame('head sha', $revisions->getHead());
    }

    public function testItCanBeTransformedToAnArray()
    {
        $revisions = new Git\Revisions('base sha', 'head sha');
        $data = $revisions->toArray();

        $this->assertSame('base sha', $data['base']);
        $this->assertSame('head sha', $data['head']);
    }
}
