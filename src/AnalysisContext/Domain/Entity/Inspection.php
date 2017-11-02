<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Domain\Entity;

class Inspection
{
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_STARTED = 'started';
    const STATUS_FINISHED = 'finished';
    const STATUS_FAILED = 'failed';

    private $id;
    private $report;
    private $status;
    private $startedAt;
    private $finishedAt;
    private $failureTrace = '';

    public function start()
    {
        if ($this->startedAt !== null) {
            //throw new \LogicException('This inspection is already started');
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
        $this->finishedAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->status = self::STATUS_FAILED;

        $this->failureTrace = $e->getMessage().$e->getTraceAsString();
    }
}
