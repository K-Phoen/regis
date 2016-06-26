<?php

declare(strict_types=1);

namespace Regis\Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Regis\Application\Model\Git\Revisions;
use Regis\Uuid;

abstract class Inspection
{
    const TYPE_GITHUB_PR = 'github_pr';

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_STARTED = 'started';
    const STATUS_FINISHED = 'finished';
    const STATUS_FAILED = 'failed';

    private $id;
    private $repository;
    /** @var ArrayCollection */
    private $analysises;
    private $status;
    private $createdAt;
    private $startedAt;
    private $finishedAt;
    private $base;
    private $head;
    private $failureTrace = '';

    abstract public function getType(): string;

    protected static function createForRevisions(Repository $repository, Revisions $revisions)
    {
        $inspection = new static(Uuid::create());
        $inspection->repository = $repository;
        $inspection->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $inspection->status = self::STATUS_SCHEDULED;
        $inspection->base = $revisions->getBase();
        $inspection->head = $revisions->getHead();

        return $inspection;
    }

    private function __construct(string $id)
    {
        $this->id = $id;
        $this->analysises = new ArrayCollection();
    }

    public function start()
    {
        if ($this->startedAt !== null) {
            throw new \LogicException('This inspection is already started');
        }

        $this->startedAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->status = self::STATUS_STARTED;
    }

    public function finish()
    {
        if ($this->finishedAt !== null) {
            throw new \LogicException('This inspection is already finished');
        }

        $this->finishedAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->status = self::STATUS_FINISHED;
    }

    public function fail(\Exception $e)
    {
        $this->finish();
        $this->status = self::STATUS_FAILED;

        $this->failureTrace = $e->getMessage().$e->getTraceAsString();
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

    public function getanalysises(): \Traversable
    {
        return $this->analysises;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt()
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

    public function addAnalysis(Analysis $analysis)
    {
        $analysis->setInspection($this);
        $this->analysises->add($analysis);
    }

    public function getRepository(): Repository
    {
        return $this->repository;
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
