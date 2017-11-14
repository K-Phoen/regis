<?php

declare(strict_types=1);

namespace Regis\AppContext\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Regis\GithubContext\Domain\Model;
use Regis\Kernel\Uuid;

class Repository
{
    const TYPE_GITHUB = 'github';

    private $id;
    private $identifier;
    private $type = self::TYPE_GITHUB;
    private $sharedSecret;
    private $isInspectionEnabled = true;
    /** @var ArrayCollection */
    private $inspections;
    /** @var ArrayCollection */
    private $teams;
    private $owner;

    public function __construct(User $owner, string $identifier = null, string $sharedSecret = null)
    {
        $this->id = Uuid::create();
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
        return $this->type;
    }

    public function getOwnerUsername(): string
    {
        return $this->toIdentifier()->getOwner();
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
