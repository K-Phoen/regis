<?php

declare(strict_types=1);

namespace Regis\GithubContext\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Regis\GithubContext\Domain\Model;

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

    /** @var UserAccount */
    private $owner;

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

    public function getOwner(): GithubDetails
    {
        return $this->owner->getDetails();
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
