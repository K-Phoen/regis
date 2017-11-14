<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Model;

class Repository
{
    private $identifier;
    private $name;
    private $cloneUrl;
    private $publicUrl;

    public static function fromArray(array $data): self
    {
        return new static(
            RepositoryIdentifier::fromArray($data['identifier']),
            $data['name'],
            $data['clone_url'],
            $data['public_url']
        );
    }

    public function __construct(RepositoryIdentifier $identifier, string $name, string $cloneUrl, string $publicUrl)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->cloneUrl = $cloneUrl;
        $this->publicUrl = $publicUrl;
    }

    public function toArray()
    {
        return [
            'identifier' => $this->identifier->toArray(),
            'name' => $this->name,
            'clone_url' => $this->cloneUrl,
            'public_url' => $this->publicUrl,
        ];
    }

    public function getIdentifier(): RepositoryIdentifier
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCloneUrl(): string
    {
        return $this->cloneUrl;
    }

    public function getPublicUrl(): string
    {
        return $this->publicUrl;
    }

    public function __toString(): string
    {
        return $this->identifier->value();
    }
}
