<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Regis\BitbucketContext\Domain\Model;

class Repository
{
    private $id;
    private $identifier;
    private $isInspectionEnabled = true;
    /** @var ArrayCollection */
    private $inspections;

    /** @var UserAccount */
    private $owner;

    public function toIdentifier(): Model\RepositoryIdentifier
    {
        return new Model\RepositoryIdentifier($this->identifier);
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getOwner(): BitbucketDetails
    {
        return $this->owner->getDetails();
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
