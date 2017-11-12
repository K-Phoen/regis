<?php

declare(strict_types=1);

namespace Regis\AnalysisContext\Domain\Model\Git;

class Repository
{
    private $cloneUrl;
    private $owner;
    private $name;

    public static function fromArray(array $data): self
    {
        return new static(
            $data['owner'],
            $data['name'],
            $data['clone_url']
        );
    }

    public function __construct(string $owner, string $name, string $cloneUrl)
    {
        $this->cloneUrl = $cloneUrl;
        $this->owner = $owner;
        $this->name = $name;
    }

    public function toArray()
    {
        return [
            'clone_url' => $this->cloneUrl,
            'owner' => $this->owner,
            'name' => $this->name,
        ];
    }

    public function getCloneUrl(): string
    {
        return $this->cloneUrl;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIdentifier(): string
    {
        return sprintf('%s/%s', $this->owner, $this->name);
    }

    public function __toString(): string
    {
        return $this->getIdentifier();
    }
}
