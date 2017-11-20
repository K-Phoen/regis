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

class Inspection
{
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_STARTED = 'started';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_FAILED = 'failed';

    private $id;
    private $report;
    private $status = self::STATUS_SCHEDULED;
    private $startedAt;
    private $finishedAt;
    private $type;
    private $failureTrace = '';

    public function id()
    {
        return $this->id;
    }

    public function status(): string
    {
        return $this->status;
    }

    /**
     * @return Report|null
     */
    public function report()
    {
        return $this->report;
    }

    public function failureTrace(): string
    {
        return $this->failureTrace;
    }

    /**
     * @return \DateTime|null
     */
    public function startedAt()
    {
        return $this->startedAt;
    }

    /**
     * @return \DateTime|null
     */
    public function finishedAt()
    {
        return $this->finishedAt;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function start()
    {
        if ($this->startedAt !== null) {
            throw new \LogicException('This inspection is already started');
        }

        $this->startedAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->status = self::STATUS_STARTED;
    }

    public function finish(Report $report)
    {
        if ($this->finishedAt !== null) {
            throw new \LogicException('This inspection is already finished');
        }

        $this->finishedAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->status = self::STATUS_FINISHED;
        $this->report = $report;
    }

    public function fail(\Exception $e)
    {
        if ($this->finishedAt !== null) {
            throw new \LogicException('This inspection is already finished');
        }

        $this->finishedAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->status = self::STATUS_FAILED;

        $this->failureTrace = $e->getMessage();
    }
}
