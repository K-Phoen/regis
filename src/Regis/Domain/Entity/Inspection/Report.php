<?php

declare(strict_types=1);

namespace Regis\Domain\Entity\Inspection;

use Doctrine\Common\Collections\ArrayCollection;
use Regis\Application\Git\DiffParser;
use Regis\Domain\Entity\Inspection;
use Regis\Domain\Model\Git\Diff;

class Report
{
    const STATUS_OK = 'ok';
    const STATUS_WARNING = 'warning';
    const STATUS_ERROR = 'error';

    private $id;
    /** @var ArrayCollection */
    private $analyses;
    private $status = self::STATUS_OK;
    private $rawDiff;
    private $inspection;

    public function __construct(string $rawDiff)
    {
        $this->analyses = new ArrayCollection();
        $this->rawDiff = $rawDiff;
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

    public function getRawDiff(): string
    {
        return stream_get_contents($this->rawDiff);
    }

    public function getDiff(): Diff
    {
        $rawDiff = $this->getRawDiff();

        $parser = new DiffParser();
        $files = $parser->parse($rawDiff);

        return new Diff($this->getInspection()->getRevisions(), $files, $rawDiff);
    }
}
