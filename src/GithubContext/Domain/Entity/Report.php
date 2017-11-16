<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Report
{
    private $id;

    /** @var ArrayCollection */
    private $analyses;
    private $warningsCount;
    private $errorsCount;

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
        return $this->errorsCount !== 0;
    }

    public function hasWarnings(): bool
    {
        return $this->warningsCount !== 0;
    }

    public function warningsCount(): int
    {
        return $this->warningsCount;
    }

    public function errorsCount(): int
    {
        return $this->errorsCount;
    }
}
