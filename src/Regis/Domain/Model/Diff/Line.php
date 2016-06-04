<?php

declare(strict_types=1);

namespace Regis\Domain\Model\Diff;

class Line
{
    protected $type;
    protected $position;
    protected $content;

    public function __construct(int $type, int $position, string $content)
    {
        $this->type = $type;
        $this->position = $position;
        $this->content = $content;
    }

    public function getChangeType(): int
    {
        return $this->type;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}