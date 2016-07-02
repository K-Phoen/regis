<?php

declare(strict_types=1);

namespace Regis\Domain\Entity\Inspection;

use Doctrine\Common\Collections\ArrayCollection;
use Regis\Domain\Entity\Inspection;

class Report
{
    const STATUS_OK = 'ok';
    const STATUS_WARNING = 'warning';
    const STATUS_ERROR = 'error';

    private $id;
    /** @var ArrayCollection */
    private $analyses;
    private $status = self::STATUS_OK;
    private $inspection;

    public function __construct()
    {
        $this->analyses = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function addAnalysis(Analysis $analysis)
    {
        $analysis->setReport($this);
        $this->analyses->add($analysis);

        if ($analysis->hasErrors()) {
            $this->status = self::STATUS_ERROR;
        }

        if ($this->status !== self::STATUS_ERROR && $analysis->hasWarnings()) {
            $this->status = self::STATUS_WARNING;
        }
    }

    public function getAnalyses(): \Traversable
    {
        return $this->analyses;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setInspection(Inspection $inspection)
    {
        $this->inspection = $inspection;
    }

    public function getInspection(): Inspection
    {
        return $this->inspection;
    }

    public function hasErrors(): bool
    {
        foreach ($this->analyses as $analysis) {
            if ($analysis->hasErrors()) {
                return true;
            }
        }

        return false;
    }

    public function errorsCount(): int
    {
        return array_reduce($this->analyses->toArray(), function(int $count, Analysis $analysis) {
            return $count + $analysis->errorsCount();
        }, 0);
    }

    public function hasWarnings(): bool
    {
        foreach ($this->analyses as $analysis) {
            if ($analysis->hasWarnings()) {
                return true;
            }
        }

        return false;
    }

    public function warningsCount(): int
    {
        return array_reduce($this->analyses->toArray(), function(int $count, Analysis $analysis) {
            return $count + $analysis->warningsCount();
        }, 0);
    }

    public function getViolations(): \Traversable
    {
        /** @var Analysis $analysis */
        foreach ($this->analyses as $analysis) {
            foreach ($analysis->getViolations() as $violation) {
                yield $violation;
            }
        }
    }
}
