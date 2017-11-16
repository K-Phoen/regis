<?php

declare(strict_types=1);

namespace Regis\AppContext\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Regis\Kernel;

class Repository
{
    const TYPE_GITHUB = 'github';
    const TYPE_BITBUCKET = 'bitbucket';

    private $id;
    private $identifier;
    private $name;
    private $type;
    private $sharedSecret;
    private $isInspectionEnabled = true;
    /** @var ArrayCollection */
    private $inspections;
    /** @var ArrayCollection */
    private $teams;
    private $owner;

    public function __construct(Kernel\User $owner, string $type, string $identifier, string $name, string $sharedSecret = null)
    {
        $this->id = Kernel\Uuid::create();
        $this->owner = $owner;
        $this->type = $type;
        $this->name = $name;
        $this->identifier = $identifier;
        $this->sharedSecret = $sharedSecret;
        $this->inspections = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    public function newSharedSecret(string $sharedSecret)
    {
        $this->sharedSecret = $sharedSecret;
    }

    public function getId(): string
    {
        return $this->type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOwner(): Kernel\User
    {
        return $this->owner;
    }

    public function getSharedSecret(): string
    {
        return $this->sharedSecret;
    }

    public function getInspections(): \Traversable
    {
        return $this->inspections;
    }

    public function getTeams(): \Traversable
    {
        return $this->teams;
    }

    public function isInspectionEnabled(): bool
    {
        return $this->isInspectionEnabled;
    }

    public function disableInspection()
    {
        $this->isInspectionEnabled = false;
    }

    public function enableInspection()
    {
        $this->isInspectionEnabled = true;
    }
}
