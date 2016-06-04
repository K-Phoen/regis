<?php

declare(strict_types=1);

namespace Regis\Domain\Model;

class Commit
{
    private $sha;

    public function __construct(string $sha)
    {
        $this->sha = $sha;
    }

    public function getSha(): string
    {
        return $this->sha;
    }
}