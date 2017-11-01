<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Domain\Model\Inspection;

use Regis\AnalysisContext\Domain\Model\Git\Diff;

class Report
{
    const STATUS_OK = 'ok';
    const STATUS_WARNING = 'warning';
    const STATUS_ERROR = 'error';

    /** @var Analysis[] */
    private $analyses = [];
    private $status = self::STATUS_OK;
    private $rawDiff;

    public function __construct(string $rawDiff)
    {
        $this->rawDiff = $rawDiff;
    }

    public function addAnalysis(Analysis $analysis)
    {
        $this->analyses[] = $analysis;

        if ($analysis->hasErrors()) {
            $this->status = self::STATUS_ERROR;
        }

        if ($this->status !== self::STATUS_ERROR && $analysis->hasWarnings()) {
            $this->status = self::STATUS_WARNING;
        }
    }

    /**
     * @return Analysis[]
     */
    public function analyses(): array
    {
        return $this->analyses;
    }

    public function status(): string
    {
        return $this->status;
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
        return array_reduce($this->analyses, function (int $count, Analysis $analysis) {
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
        return array_reduce($this->analyses, function (int $count, Analysis $analysis) {
            return $count + $analysis->warningsCount();
        }, 0);
    }

    public function violations(): \Traversable
    {
        foreach ($this->analyses as $analysis) {
            foreach ($analysis->violations() as $violation) {
                yield $violation;
            }
        }
    }

    public function rawDiff(): string
    {
        return stream_get_contents($this->rawDiff);
    }

    public function diff(): Diff
    {
        return Diff::fromRawDiff($this->getInspection()->getRevisions(), $this->getRawDiff());
    }

    public function violationsAtLine(string $file, int $line): \Traversable
    {
        /** @var Analysis $analysis */
        foreach ($this->analyses as $analysis) {
            yield from $analysis->getViolationsAtLine($file, $line);
        }
    }
}
