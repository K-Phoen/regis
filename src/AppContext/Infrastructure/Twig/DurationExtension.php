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

namespace Regis\AppContext\Infrastructure\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DurationExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('duration', [$this, 'durationFilter']),
        ];
    }

    public function durationFilter(int $seconds, string $separator = ' '): string
    {
        $units = [
            'year(s)' => 365 * 24 * 3600,
            'day(s)' => 24 * 3600,
            'hour(s)' => 3600,
            'min' => 60,
            'sec' => 1,
        ];
        $parts = [];
        $remaining = $seconds;

        foreach ($units as $unitLabel => $unit) {
            $value = (int) ($remaining / $unit);
            $remaining -= $value * $unit;

            if ($value) {
                $parts[] = $value.' '.$unitLabel;
            }
        }

        return implode($separator, $parts);
    }
}
