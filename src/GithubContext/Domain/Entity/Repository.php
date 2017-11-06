<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Regis\GithubContext\Domain\Model;

class Repository
{
    const TYPE_GITHUB = 'github';

    private $identifier;
    private $sharedSecret;
    private $isInspectionEnabled = true;
    /** @var ArrayCollection */
    private $inspections;
    /** @var ArrayCollection */
    private $teams;
    private $owner;

    public function __construct(User $owner, string $identifier = null, string $sharedSecret = null)
    {
        $this->owner = $owner;
        $this->identifier = $identifier;
        $this->sharedSecret = $sharedSecret;
        $this->inspections = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    public function newSharedSecret(string $sharedSecret)
    {
        $this->sharedSecret = $sharedSecret;
    }

    public function getType(): string
    {
        return self::TYPE_GITHUB;
    }

    public function getOwnerUsername(): string
    {
        return $this->owner->getUsername();
    }

    public function toIdentifier(): Model\RepositoryIdentifier
    {
        return Model\RepositoryIdentifier::fromFullName($this->identifier);
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
        return $this->toIdentifier()->getName();
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
