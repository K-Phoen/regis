<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Regis\BitbucketContext\Domain\Model;
use Regis\Kernel;

class Repository
{
    const TYPE_BITBUCKET = 'bitbucket';

    private $id;
    private $identifier;
    private $type = self::TYPE_BITBUCKET;
    private $isInspectionEnabled = true;
    /** @var ArrayCollection */
    private $inspections;
    private $owner;

    public function __construct(Kernel\User $owner, string $identifier)
    {
        $this->id = Kernel\Uuid::create();
        $this->owner = $owner;
        $this->identifier = $identifier;
        $this->inspections = new ArrayCollection();
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

    public function getOwner(): Kernel\User
    {
        return $this->owner;
    }

    public function getInspections(): \Traversable
    {
        return $this->inspections;
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
