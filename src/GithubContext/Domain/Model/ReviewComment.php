<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Model;

use Regis\GithubContext\Domain\Entity\Violation;

class ReviewComment
{
    private $content;
    private $position;
    private $file;

    public static function fromViolation(Violation $violation): self
    {
        return new static($violation->file(), $violation->position(), $violation->description());
    }

    public function __construct(string $file, int $position, string $content)
    {
        $this->file = $file;
        $this->position = $position;
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getFile(): string
    {
        return $this->file;
    }
}
