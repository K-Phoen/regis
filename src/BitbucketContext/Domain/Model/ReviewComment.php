<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Model;

use Regis\BitbucketContext\Domain\Entity\Violation;

class ReviewComment
{
    private $content;
    private $line;
    private $file;

    public static function fromViolation(Violation $violation): self
    {
        return new static($violation->file(), $violation->line(), $violation->description());
    }

    public function __construct(string $file, int $line, string $content)
    {
        $this->file = $file;
        $this->line = $line;
        $this->content = $content;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function line(): int
    {
        return $this->line;
    }

    public function file(): string
    {
        return $this->file;
    }
}
