<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Report
{
    private $id;

    /** @var ArrayCollection */
    private $analyses;

    public function getId(): string
    {
        return $this->id;
    }

    public function analyses(): \Traversable
    {
        return $this->analyses;
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
        return array_reduce($this->analyses->toArray(), function (int $count, Analysis $analysis) {
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
        return array_reduce($this->analyses->toArray(), function (int $count, Analysis $analysis) {
            return $count + $analysis->warningsCount();
        }, 0);
    }
}
