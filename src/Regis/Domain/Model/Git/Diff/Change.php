<?php

declare(strict_types=1);

namespace Regis\Domain\Model\Git\Diff;

class Change
{
    const LINE_CONTEXT = 0;
    const LINE_REMOVE = -1;
    const LINE_ADD = 1;

    protected $rangeOldStart;
    protected $rangeOldCount;
    protected $rangeNewStart;
    protected $rangeNewCount;
    /** @var Line[] */
    protected $lines;

    public function __construct(int $rangeOldStart, int $rangeOldCount, int $rangeNewStart, int $rangeNewCount, array $lines)
    {
        $this->rangeOldStart = $rangeOldStart;
        $this->rangeOldCount = $rangeOldCount;
        $this->rangeNewStart = $rangeNewStart;
        $this->rangeNewCount = $rangeNewCount;
        $this->lines = $lines;
    }

    public function getRangeOldStart(): int
    {
        return $this->rangeOldStart;
    }

    public function getRangeOldCount(): int
    {
        return $this->rangeOldCount;
    }

    public function getRangeNewStart(): int
    {
        return $this->rangeNewStart;
    }

    public function getRangeNewCount(): int
    {
        return $this->rangeNewCount;
    }

    public function getLines(): array
    {
        return $this->lines;
    }

    public function getAddedLines(): \Traversable
    {
        foreach ($this->lines as $line) {
            if ($line->getChangeType() === self::LINE_ADD) {
                yield $line;
            }
        }
    }
}