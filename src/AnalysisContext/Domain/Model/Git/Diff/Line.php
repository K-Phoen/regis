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

class Line
{
    private $type;
    private $position;
    private $content;
    private $number;

    public function __construct(int $type, int $position, int $number, string $content)
    {
        $this->type = $type;
        $this->position = $position;
        $this->number = $number;
        $this->content = $content;
    }

    public function isAddition(): bool
    {
        return $this->type === Change::LINE_ADD;
    }

    public function isDeletion(): bool
    {
        return $this->type === Change::LINE_REMOVE;
    }

    public function isContext(): bool
    {
        return $this->type === Change::LINE_CONTEXT;
    }

    public function getChangeType(): int
    {
        return $this->type;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
