<?php

declare(strict_types=1);

namespace Regis\Domain\Model\Git\Diff;

class Line
{
    private $type;
    private $position;
    private $content;
    private $number;

    public function __construct(int $type, int $position, int $number, string $content)
    {
        $this->type = $type;
        $this->position = $position;
        $this->number = $number;
        $this->content = $content;
    }

    public function isAddition(): bool
    {
        return $this->type === Change::LINE_ADD;
    }

    public function isDeletion(): bool
    {
        return $this->type === Change::LINE_REMOVE;
    }

    public function isContext(): bool
    {
        return $this->type === Change::LINE_CONTEXT;
    }

    public function getChangeType(): int
    {
        return $this->type;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
