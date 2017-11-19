<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Domain\Model\Git;

class Repository
{
    private $cloneUrl;
    private $identifier;

    public static function fromArray(array $data): self
    {
        return new static(
            $data['identifier'],
            $data['clone_url']
        );
    }

    public function __construct(string $identifier, string $cloneUrl)
    {
        $this->cloneUrl = $cloneUrl;
        $this->identifier = $identifier;
    }

    public function toArray()
    {
        return [
            'identifier' => $this->identifier,
            'clone_url' => $this->cloneUrl,
        ];
    }

    public function getCloneUrl(): string
    {
        return $this->cloneUrl;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function __toString(): string
    {
        return $this->identifier;
    }
}
