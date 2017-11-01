<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Model;

class Repository
{
    private $identifier;
    private $publicUrl;
    private $cloneUrl;

    public static function fromArray(array $data): Repository
    {
        return new static(
            RepositoryIdentifier::fromArray($data['identifier']),
            $data['publicUrl'],
            $data['cloneUrl']
        );
    }

    public function __construct(RepositoryIdentifier $identifier, string $publicUrl, string $cloneUrl)
    {
        $this->identifier = $identifier;
        $this->publicUrl = $publicUrl;
        $this->cloneUrl = $cloneUrl;
    }

    public function toArray()
    {
        return [
            'identifier' => $this->identifier->toArray(),
            'publicUrl' => $this->publicUrl,
            'cloneUrl' => $this->cloneUrl,
        ];
    }

    public function getIdentifier(): string
    {
        return $this->identifier->getIdentifier();
    }

    public function getOwner(): string
    {
        return $this->identifier->getOwner();
    }

    public function getName(): string
    {
        return $this->identifier->getName();
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
