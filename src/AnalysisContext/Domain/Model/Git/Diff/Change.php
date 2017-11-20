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

namespace Regis\AnalysisContext\Domain\Model\Git\Diff;

class Change
{
    public const LINE_CONTEXT = 0;
    public const LINE_REMOVE = -1;
    public const LINE_ADD = 1;

    protected $rangeOldStart;
    protected $rangeOldCount;
    protected $rangeNewStart;
    protected $rangeNewCount;
    /** @var Line[] */
    protected $lines;

    public function __construct(int $rangeOldStart, int $rangeOldCount, int $rangeNewStart, int $rangeNewCount, array $lines)
    {
        $this->rangeOldStart = $rangeOldStart;
        $this->rangeOldCount = $rangeOldCount;
        $this->rangeNewStart = $rangeNewStart;
        $this->rangeNewCount = $rangeNewCount;
        $this->lines = $lines;
    }

    public function getRangeOldStart(): int
    {
        return $this->rangeOldStart;
    }

    public function getRangeOldCount(): int
    {
        return $this->rangeOldCount;
    }

    public function getRangeNewStart(): int
    {
        return $this->rangeNewStart;
    }

    public function getRangeNewCount(): int
    {
        return $this->rangeNewCount;
    }

    public function getLines(): array
    {
        return $this->lines;
    }
}
