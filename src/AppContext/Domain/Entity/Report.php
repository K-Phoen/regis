<?php

declare(strict_types=1);

namespace Regis\AppContext\Domain\Entity;

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
    private $warningsCount;
    private $errorsCount;

    public function getId(): string
    {
        return $this->id;
    }

    public function rawDiff(): string
    {
        return is_resource($this->rawDiff) ? stream_get_contents($this->rawDiff) : $this->rawDiff;
    }

    public function analyses(): \Traversable
    {
        return $this->analyses;
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

    public function violationsAtLine(string $file, int $line): \Traversable
    {
        /** @var Analysis $analysis */
        foreach ($this->analyses as $analysis) {
            yield from $analysis->violationsAtLine($file, $line);
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
