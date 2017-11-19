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

namespace Regis\BitbucketContext\Domain\Model;

use Regis\BitbucketContext\Domain\Entity\Violation;

class ReviewComment
{
    private $content;
    private $line;
    private $file;

    public static function fromViolation(Violation $violation): self
    {
        return new static($violation->file(), $violation->line(), $violation->description());
    }

    public function __construct(string $file, int $line, string $content)
    {
        $this->file = $file;
        $this->line = $line;
        $this->content = $content;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function line(): int
    {
        return $this->line;
    }

    public function file(): string
    {
        return $this->file;
    }
}
