<?php

declare(strict_types=1);

namespace Regis\Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Regis\Application\Model\Github\PullRequest;
use Regis\Uuid;

class Inspection
{
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_STARTED = 'started';
    const STATUS_FINISHED = 'finished';
    const STATUS_FAILED = 'failed';

    private $id;
    private $repository;
    /** @var ArrayCollection */
    private $analysises;
    private $pullRequestId;
    private $status;
    private $createdAt;
    private $startedAt;
    private $finishedAt;
    private $failureTrace = '';

    public static function create(Repository $repository, PullRequest $pullRequest)
    {
        $inspection = new static(Uuid::create());
        $inspection->pullRequestId = $pullRequest->getNumber();
        $inspection->repository = $repository;
        $inspection->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $inspection->status = self::STATUS_SCHEDULED;

        return $inspection;
    }

    public function __construct(string $id)
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

    public function getPullRequestId(): int
    {
        return $this->pullRequestId;
    }
}
