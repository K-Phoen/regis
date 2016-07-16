<?php

declare(strict_types=1);

namespace Regis\Domain\Model\Github;

class Repository
{
    private $identifier;
    private $publicUrl;
    private $cloneUrl;

    public function __construct(string $identifier, string $publicUrl, string $cloneUrl)
    {
        $this->identifier = $identifier;
        $this->publicUrl = $publicUrl;
        $this->cloneUrl = $cloneUrl;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getPublicUrl(): string
    {
        return $this->publicUrl;
    }

    public function getCloneUrl(): string
    {
        return $this->cloneUrl;
    }
}