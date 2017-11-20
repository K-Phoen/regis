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

use Regis\AnalysisContext\Domain\Model\Git\Diff;
use Regis\AnalysisContext\Domain\Model\Git\Revisions;

class Inspection
{
    public const TYPE_GITHUB_PR = 'github_pr';

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_STARTED = 'started';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_FAILED = 'failed';

    private $id;
    private $report;
    private $status;
    private $repository;
    private $createdAt;
    private $startedAt;
    private $finishedAt;
    private $type;
    private $base;
    private $head;
    private $failureTrace = '';

    public function getId(): string
    {
        return $this->id;
    }

    // TODO
    public function getDiff(): Diff
    {
        return Diff::fromRawDiff(
            new Revisions($this->getBase(), $this->getHead()),
            $this->getReport()->rawDiff()
        );
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getReport(): Report
    {
        return $this->report;
    }

    public function hasReport(): bool
    {
        return $this->report !== null;
    }

    public function getFailureTrace(): string
    {
        return $this->failureTrace;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    public function getHead(): string
    {
        return $this->head;
    }

    public function getBase(): string
    {
        return $this->base;
    }
}
