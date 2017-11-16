<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Analysis
{
    private $id;
    private $report;

    /** @var ArrayCollection<Violation> */
    private $violations = [];

    private $errorsCount;
    private $warningsCount;

    /**
     * @return Violation[]
     */
    public function violations(): array
    {
        return $this->violations->toArray();
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
