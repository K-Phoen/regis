<?php

declare(strict_types=1);

namespace Regis\AppContext\Domain\Model;

class Repository
{
    private $identifier;
    private $name;
    private $type;
    private $publicUrl;

    public function __construct(string $identifier, string $name, string $publicUrl, string $type)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->publicUrl = $publicUrl;
        $this->type = $type;
    }

    public function toArray()
    {
        return [
            'identifier' => $this->identifier,
            'name' => $this->name,
            'public_url' => $this->publicUrl,
            'type' => $this->type,
        ];
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPublicUrl(): string
    {
        return $this->publicUrl;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
