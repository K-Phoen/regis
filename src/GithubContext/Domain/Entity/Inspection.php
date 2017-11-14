<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Entity;

use Regis\Kernel\Uuid;

abstract class Inspection
{
    const TYPE_GITHUB_PR = 'github_pr';

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_STARTED = 'started';
    const STATUS_FINISHED = 'finished';
    const STATUS_FAILED = 'failed';

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
