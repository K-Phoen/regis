<?php

declare(strict_types=1);

namespace Regis\AppContext\Domain\Entity;

use Regis\AnalysisContext\Domain\Model\Git\Diff;
use Regis\AnalysisContext\Domain\Model\Git\Revisions;

class Inspection
{
    const TYPE_GITHUB_PR = 'github_pr';

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_STARTED = 'started';
    const STATUS_FINISHED = 'finished';
    const STATUS_FAILED = 'failed';

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
