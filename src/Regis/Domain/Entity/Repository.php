<?php

declare(strict_types=1);

namespace Regis\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;

abstract class Repository
{
    const TYPE_GITHUB = 'github';

    private $identifier;
    private $sharedSecret;
    /** @var ArrayCollection */
    private $inspections;
    private $owner;

    abstract public function getType(): string;

    public function __construct(User $owner, string $identifier = null, string $sharedSecret = null)
    {
        $this->owner = $owner;
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

    public function getOwner(): User
    {
        return $this->owner;
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
