<?php

declare(strict_types=1);

namespace Regis\Domain\Model;

class ReviewComment
{
    private $content;
    private $commit;
    private $position;
    private $file;

    public static function fromViolation(Violation $violation): ReviewComment
    {
        return new static(
            $violation->getFile(), $violation->getPosition(),
            $violation->getDescription(), $violation->getCommit() ? $violation->getCommit()->getSha() : null
        );
    }

    public function __construct(string $file, int $position, string $content, string $commit = null)
    {
        $this->file = $file;
        $this->position = $position;
        $this->content = $content;
        $this->commit = $commit;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getCommit()
    {
        return $this->commit;
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