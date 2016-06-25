<?php

declare(strict_types=1);

namespace Regis\Application\Command\Repository;

class Create
{
    private $identifier;
    private $sharedSecret;

    public function __construct(string $identifier, string $sharedSecret)
    {
        $this->identifier = $identifier;
        $this->sharedSecret = $sharedSecret;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getSharedSecret(): string
    {
        return $this->sharedSecret;
    }
}