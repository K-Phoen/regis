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

namespace Regis\AppContext\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Analysis
{
    public const STATUS_OK = 'ok';
    public const STATUS_WARNING = 'warning';
    public const STATUS_ERROR = 'error';

    private $id;
    private $type;
    private $report;

    /** @var ArrayCollection<Violation> */
    private $violations;

    private $violationsMap;
    private $errorsCount;
    private $warningsCount;

    public function id(): string
    {
        return $this->id;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function report(): Report
    {
        return $this->report;
    }

    public function status(): string
    {
        if ($this->hasErrors()) {
            return self::STATUS_ERROR;
        }

        if ($this->hasWarnings()) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_OK;
    }

    /**
     * @return Violation[]
     */
    public function violations(): array
    {
        return $this->violations->toArray();
    }

    public function violationsAtLine(string $file, int $line): array
    {
        if ($this->violationsMap === null) {
            $this->buildViolationsMap();
        }

        if (!isset($this->violationsMap[sprintf('%s:%d', $file, $line)])) {
            return [];
        }

        return $this->violationsMap[sprintf('%s:%d', $file, $line)];
    }

    private function buildViolationsMap()
    {
        $this->violationsMap = [];

        /** @var Violation $violation */
        foreach ($this->violations as $violation) {
            $key = sprintf('%s:%d', $violation->file(), $violation->line());

            if (!isset($this->violationsMap[$key])) {
                $this->violationsMap[$key] = [];
            }

            $this->violationsMap[$key][] = $violation;
        }
    }

    public function hasErrors(): bool
    {
        return $this->errorsCount !== 0;
    }

    public function hasWarnings(): bool
    {
        return $this->warningsCount !== 0;
    }

    public function warningsCount(): int
    {
        return $this->warningsCount;
    }

    public function errorsCount(): int
    {
        return $this->errorsCount;
    }
}
