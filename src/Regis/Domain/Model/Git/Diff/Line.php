<?php

declare(strict_types=1);

namespace Regis\Domain\Model\Git\Diff;

class Line
{
    protected $type;
    protected $position;

    public function __construct(int $type, int $position)
    {
        $this->type = $type;
        $this->position = $position;
    }

    public function getChangeType(): int
    {
        return $this->type;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}