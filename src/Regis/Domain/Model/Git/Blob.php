<?php

declare(strict_types=1);

namespace Regis\Domain\Model\Git;

class Blob
{
    private $hash;
    private $content;
    private $mimetype;

    public function __construct(string $hash, string $content, string $mimeType)
    {
        $this->hash = $hash;
        $this->content = $content;
        $this->mimetype = $mimeType;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getMimetype(): string
    {
        return $this->mimetype;
    }
}
