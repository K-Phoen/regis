<?php

declare(strict_types=1);

namespace Regis\Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Repository
{
    private $identifier;
    private $sharedSecret;
    /** @var ArrayCollection */
    private $inspections;

    public function __construct(string $identifier = null, string $sharedSecret = null)
    {
        $this->identifier = $identifier;
        $this->sharedSecret = $sharedSecret;
        $this->inspections = new ArrayCollection();
    }

    public function newSharedSecret(string $sharedSecret)
    {
        $this->sharedSecret = $sharedSecret;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getOwner(): string
    {
        return explode('/', $this->identifier)[0];
    }

    public function getName(): string
    {
        return explode('/', $this->identifier)[1];
    }

    public function getSharedSecret(): string
    {
        return $this->sharedSecret;
    }

    public function getInspections(): \Traversable
    {
        return $this->inspections;
    }
}
