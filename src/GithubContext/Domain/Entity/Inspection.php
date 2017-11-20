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

namespace Regis\GithubContext\Domain\Entity;

use Regis\Kernel\Uuid;

abstract class Inspection
{
    public const TYPE_GITHUB_PR = 'github_pr';

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_STARTED = 'started';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_FAILED = 'failed';

    private $id;
    private $report;
    private $repository;
    private $status;
    private $createdAt;
    private $startedAt;
    private $finishedAt;
    private $base;
    private $head;
    private $failureTrace = '';

    abstract public function getType(): string;

    protected static function createForRevisions(Repository $repository, string $head, string $base): self
    {
        $inspection = new static();
        $inspection->repository = $repository;
        $inspection->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $inspection->status = self::STATUS_SCHEDULED;
        $inspection->base = $base;
        $inspection->head = $head;

        return $inspection;
    }

    private function __construct()
    {
        $this->id = Uuid::create();
    }

    public function getReport(): Report
    {
        return $this->report;
    }

    public function getRepository(): Repository
    {
        return $this->repository;
    }

    public function hasReport(): bool
    {
        return $this->report !== null;
    }

    public function getFailureTrace(): string
    {
        return $this->failureTrace;
    }

    public function getId(): string
    {
        return $this->id;
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
