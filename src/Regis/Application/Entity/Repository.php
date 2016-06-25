<?php

declare(strict_types=1);

namespace Regis\Application\Entity;

class Repository
{
    private $identifier;
    private $sharedSecret;

    public function __construct(string $identifier = null, string $sharedSecret = null)
    {
        $this->identifier = $identifier;
        $this->sharedSecret = $sharedSecret;
    }

    public function setIdentifier(string $identifier)
    {
        if ($this->identifier !== null) {
            throw new \LogicException('This repository already has an identifier');
        }

        $this->identifier = $identifier;
    }

    public function setSharedSecret(string $sharedSecret)
    {
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
