<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Model;

class RepositoryIdentifier
{
    private $owner;
    private $name;

    public static function fromFullName(string $fullName): self
    {
        $parts = explode('/', $fullName);

        if (count($parts) !== 2) {
            throw new \InvalidArgumentException(sprintf('Invalid full name "%s"', $fullName));
        }

        return new static($parts[0], $parts[1]);
    }

    public static function fromArray(array $data): self
    {
        return new static(
            $data['owner'],
            $data['name']
        );
    }

    public function __construct(string $owner, string $name)
    {
        $this->owner = $owner;
        $this->name = $name;
    }

    public function toArray()
    {
        return [
            'owner' => $this->owner,
            'name' => $this->name,
        ];
    }

    public function getIdentifier(): string
    {
        return $this->owner.'/'.$this->name;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->getIdentifier();
    }
}
