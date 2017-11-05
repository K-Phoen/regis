<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;

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
        $this->analyses->add($analysis);

        if ($analysis->hasErrors()) {
            $this->status = self::STATUS_ERROR;
        }

        if ($this->status !== self::STATUS_ERROR && $analysis->hasWarnings()) {
            $this->status = self::STATUS_WARNING;
        }
    }

    public function analyses(): array
    {
        return $this->analyses->toArray();
    }

    public function status(): string
    {
        return $this->status;
    }

    public function violations(): \Traversable
    {
        /** @var Analysis $analysis */
        foreach ($this->analyses as $analysis) {
            foreach ($analysis->violations() as $violation) {
                yield $violation;
            }
        }
    }
}
