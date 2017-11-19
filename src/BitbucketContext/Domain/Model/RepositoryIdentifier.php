<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Model;

class RepositoryIdentifier
{
    private $value;

    public static function fromArray(array $data): self
    {
        return new static($data['identifier']);
    }

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function toArray()
    {
        return [
            'identifier' => $this->value,
        ];
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->value;
    }
}
