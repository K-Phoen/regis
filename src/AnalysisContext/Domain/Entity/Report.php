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

namespace Regis\AnalysisContext\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Regis\Kernel;

class Report
{
    public const STATUS_OK = 'ok';
    public const STATUS_WARNING = 'warning';
    public const STATUS_ERROR = 'error';

    private $id;
    private $warningsCount = 0;
    private $errorsCount = 0;
    /** @var ArrayCollection<Analysis> */
    private $analyses;
    private $status = self::STATUS_OK;
    private $rawDiff;

    public function __construct(string $rawDiff)
    {
        $this->id = Kernel\Uuid::create();
        $this->analyses = new ArrayCollection();
        $this->rawDiff = $rawDiff;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function addAnalysis(Analysis $analysis)
    {
        $this->analyses->add($analysis);

        $this->errorsCount += $analysis->errorsCount();
        $this->warningsCount += $analysis->warningsCount();

        if ($analysis->hasErrors()) {
            $this->status = self::STATUS_ERROR;
        }

        if ($this->status !== self::STATUS_ERROR && $analysis->hasWarnings()) {
            $this->status = self::STATUS_WARNING;
        }
    }

    public function status(): string
    {
        return $this->status;
    }

    public function warningsCount(): int
    {
        return $this->warningsCount;
    }

    public function errorsCount(): int
    {
        return $this->errorsCount;
    }

    public function violations(): \Traversable
    {
        /** @var Analysis $analysis */
        foreach ($this->analyses as $analysis) {
            foreach ($analysis->violations() as $violation) {
                yield $violation;
            }
        }
    }
}
