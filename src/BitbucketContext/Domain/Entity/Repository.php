<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Regis\BitbucketContext\Domain\Model;
use Regis\Kernel\Uuid;

class Repository
{
    const TYPE_BITBUCKET = 'bitbucket';

    private $id;
    private $identifier;
    private $type = self::TYPE_BITBUCKET;
    private $sharedSecret = '';
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
        $this->inspections = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function toIdentifier(): Model\RepositoryIdentifier
    {
        return new Model\RepositoryIdentifier($this->identifier);
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getOwner(): User
    {
        return $this->owner;
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
